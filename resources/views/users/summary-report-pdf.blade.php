<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>التقرير الملخص</title>
    <style>
        * {
            direction: rtl;
            text-align: right;
        }

        body {
            font-family: 'Dejavu Sans', Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #ffffff;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #035944;
            padding-bottom: 15px;
        }

        .app-logo {
            text-align: center;
            margin-bottom: 10px;
        }

        .app-logo img {
            max-width: 120px;
        }

        .app-name {
            text-align: center;
            color: #035944;
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .header h1 {
            text-align: right;
            color: #035944;
            font-size: 20px;
            margin: 0 0 8px 0;
        }

        .header p {
            text-align: right;
            color: #666;
            font-size: 12px;
            margin: 3px 0;
        }

        .user-info {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .user-info h3 {
            color: #035944;
            margin: 0 0 8px 0;
            font-size: 16px;
        }

        .user-info-grid {
            display: table;
            width: 100%;
        }

        .user-info-row {
            display: table-row;
        }

        .user-info-cell {
            display: table-cell;
            width: 50%;
            padding: 2px 8px;
            font-size: 12px;
        }

        .user-info p {
            margin: 2px 0;
            font-size: 12px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: right;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
            font-size: 12px;
        }

        .table td {
            font-size: 12px;
        }

        .percentage {
            font-weight: bold;
            color: #035944;
        }

        .final-total {
            background-color: #035944;
            color: white;
            padding: 8px;
            border-radius: 8px;
            text-align: center;
            margin-top: 15px;
        }

        .final-total h3 {
            margin: 0 0 5px 0;
            color: white;
            font-size: 14px;
        }

        .final-total .value {
            font-size: 18px;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="app-logo">
            @php
                $fullPath = storage_path('app/public/' . \App\Helpers\SettingsHelper::get('app_logo'));
                $base64 = base64_encode(file_get_contents($fullPath));
            @endphp
            <img src="data:image/png;base64,{{ $base64 }}" alt="{{ \App\Helpers\SettingsHelper::appName() }}" >
        </div>
        <div class="app-name">
            {{ \App\Helpers\SettingsHelper::get('app_name') }}
        </div>
        <h1>التقرير الملخص</h1>
        <p>من:  {{ $start->format('d/m/Y') }} إلى: {{ $end->format('d/m/Y') }}</p>
    </div>

            <div class="user-info">
                <div class="user-info-grid">
                    <div class="user-info-row">
                        <div class="user-info-cell">
                            <p>{{ $user->department->name ?? 'لا يوجد قسم' }} <strong>القسم:</strong></p>
                        </div>
                        <div class="user-info-cell">
                            <p>{{ $user->name }} <strong>الاسم الكامل:</strong></p>
                        </div>
                    </div>
                    <div class="user-info-row">
                        <div class="user-info-cell">
                            <p>{{ $user->position ?? 'لا يوجد مسمى وظيفي' }} <strong>المسمى الوظيفي:</strong></p>
                        </div>
                        <div class="user-info-cell">
                            <p>{{ $user->shift_end ? \Carbon\Carbon::parse($user->shift_end)->format('H:i') : 'لا يوجد' }} - {{ $user->shift_start ? \Carbon\Carbon::parse($user->shift_start)->format('H:i') : 'لا يوجد' }} <strong>وقت الدوام:</strong></p>
                        </div>
                    </div>
                </div>
            </div>

    <!-- Summary Table -->
    <table class="table">
        <thead>
            <tr>
                <th>النسبة المئوية</th>
                <th>معايير التقييم</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="percentage">{{ isset($summaryData['discipline']['percentage']) ? $summaryData['discipline']['percentage'] : '0' }}%</td>
                <td>الإنضباط</td>
            </tr>
            <tr>
                <td class="percentage">{{ isset($summaryData['taskCompletion']) ? $summaryData['taskCompletion'] : '0' }}%</td>
                <td>إنجاز المهام</td>
            </tr>
            <tr>
                <td class="percentage">{{ isset($summaryData['taskQuality']) ? $summaryData['taskQuality'] : '0' }}%</td>
                <td>جودة المهام</td>
            </tr>
            <tr>
                <td class="percentage">{{ isset($summaryData['timeQuality']) ? $summaryData['timeQuality'] : '0' }}%</td>
                <td>كفاءة إستثمار الوقت</td>
            </tr>
            @if (isset($summaryData['evaluationCriteria']) && count($summaryData['evaluationCriteria']) > 0)
                @foreach ($summaryData['evaluationCriteria'] as $criteria)
                    <tr>
                        <td class="percentage">{{ $criteria['percentage'] }}%</td>
                        <td>{{ $criteria['name'] }}</td>
                    </tr>
                @endforeach
            @endif
        </tbody>
    </table>

    <!-- Final Total -->
    <div class="final-total">
        <h3>المجموع النهائي</h3>
        <div class="value">{{ $summaryData['finalTotal'] }}%</div>
    </div>

    <div style="margin-top: 30px; text-align: center; color: #666; font-size: 12px;">
        <p>تم الإنشاء في: {{ now()->format('d/m/Y') }} الساعة {{ now()->format('s:i:H') }}</p>
    </div>
</body>

</html>
