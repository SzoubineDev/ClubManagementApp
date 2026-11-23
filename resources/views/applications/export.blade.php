<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recruitment Applications</title>
    <style>
        @page {
            margin: 26px 28px;
        }

        body {
            font-family: "DejaVu Sans", sans-serif;
            font-size: 8px;
            line-height: 1.4;
            color: #171411;
        }

        .header {
            border-bottom: 2px solid #660C1B;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .club {
            color: #660C1B;
            font-size: 8px;
            font-weight: bold;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        h1 {
            margin: 3px 0 2px;
            font-size: 18px;
            line-height: 1.15;
        }

        .meta {
            color: #555;
            font-size: 8px;
        }

        .summary {
            margin: 8px 0 10px;
            font-size: 9px;
        }

        .summary strong {
            color: #660C1B;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead th {
            background: #660C1B;
            color: #FFF9EF;
            font-size: 7px;
            font-weight: bold;
            letter-spacing: .5px;
            padding: 6px 4px;
            text-align: left;
            text-transform: uppercase;
        }

        tbody td {
            border-bottom: 1px solid #DDD5C7;
            padding: 5px 4px;
            vertical-align: top;
        }

        tbody tr:nth-child(even) td {
            background: #FAF6EE;
        }

        .name {
            font-weight: bold;
        }

        .email {
            color: #555;
            font-size: 7px;
        }

        .motivation {
            font-size: 7px;
            line-height: 1.35;
        }

        .empty {
            color: #777;
            padding: 18px 4px;
            text-align: center;
        }

        .footer {
            color: #777;
            font-size: 7px;
            margin-top: 10px;
            text-align: right;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="club">The Great Debaters</div>
        <h1>Recruitment Applications</h1>
        <div class="meta">{{ $sectionLabel }} · Generated {{ $generatedAt }}</div>
    </div>

    <div class="summary">
        Total applications: <strong>{{ $total }}</strong>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 17%;">Applicant</th>
                <th style="width: 10%;">Phone</th>
                <th style="width: 9%;">CNE</th>
                <th style="width: 11%;">Filière</th>
                <th style="width: 7%;">Section</th>
                <th style="width: 8%;">Status</th>
                <th style="width: 9%;">Submitted</th>
                <th style="width: 25%;">Motivation</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $application)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>
                    <span class="name">{{ $application->full_name }}</span><br>
                    <span class="email">{{ $application->email }}</span>
                </td>
                <td>{{ $application->phone_number }}</td>
                <td>{{ $application->cne }}</td>
                <td>{{ $application->filiere }}</td>
                <td>{{ $application->sectionLabel() }}</td>
                <td>{{ $application->statusLabel() }}</td>
                <td>{{ $application->created_at?->format('Y-m-d') }}</td>
                <td class="motivation">{{ $application->motivation }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="9" class="empty">No applications found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">The Great Debaters · Recruitment export</div>

</body>
</html>
