<!DOCTYPE html>
<html>
<head>
    <title>Apply for {{ $jobPosting->ejob->EJOB_NAME ?? 'Job' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            margin-top: 20px;
            padding: 10px 15px;
            border: none;
            border-radius: 6px;
            background-color: #009d2f;
            color: white;
            cursor: pointer;
        }

        .cancel-btn {
            background-color: #ff0000;
            margin-left: 10px;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 6px;
            color: white;
            display: inline-block;
        }

        .success {
            color: green;
            margin-top: 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Apply for: {{ $jobPosting->ejob->EJOB_NAME ?? 'Job' }}</h1>

        <p><strong>Department:</strong> {{ $jobPosting->department->DP_NAME ?? 'N/A' }}</p>
        <p><strong>Qualification:</strong> {{ $jobPosting->QUALIFICATION }}</p>

        @if(session('success'))
            <p class="success">{{ session('success') }}</p>
        @endif

        <form action="{{ route('apply.submit', $jobPosting->id) }}" method="POST">
            @csrf

            <label for="FNAME">First Name:</label>
            <input type="text" name="FNAME" id="FNAME" required>

            <label for="MNAME">Middle Name:</label>
            <input type="text" name="MNAME" id="MNAME">

            <label for="LNAME">Last Name:</label>
            <input type="text" name="LNAME" id="LNAME" required>

            <button type="submit">Submit Application</button>
            <a href="{{ route('home') }}" class="cancel-btn">Cancel</a>
        </form>
    </div>

</body>
</html>
