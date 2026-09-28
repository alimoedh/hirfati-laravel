<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>فاتورة {{ $invoiceNumber }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Cairo', sans-serif;
            background: #F1F5F9;
            color: #1E293B;
            padding: 20px;
        }

        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(15, 30, 60, 0.08);
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 3px solid #D4A24C;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }

        .header h1 { font-size: 32px; color: #0F1E3C; margin-bottom: 5px; }
        .header .subtitle { color: #64748B; font-size: 14px; }

        .invoice-badge {
            background: linear-gradient(135deg, #D4A24C, #B8873A);
            color: white;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 800;
            font-size: 18px;
            margin-bottom: 10px;
        }

        .invoice-meta { font-size: 12px; color: #475569; text-align: left; }
        .invoice-meta strong { color: #0F1E3C; }

        .status-badge {
            display: inline-block;
            padding: 6px 14px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 700;
            margin-top: 10px;
        }
        .status-completed { background: #D1FAE5; color: #065F46; }
        .status-accepted  { background: #DBEAFE; color: #1E40AF; }
        .status-pending   { background: #FEF3C7; color: #92400E; }
        .status-progress  { background: #EDE9FE; color: #5B21B6; }
        .status-cancelled { background: #FEE2E2; color: #991B1B; }

        /* Sections */
        .section { margin-bottom: 25px; }

        .section-title {
            background: #F1F5F9;
            padding: 10px 15px;
            border-right: 4px solid #D4A24C;
            font-weight: 800;
            font-size: 16px;
            margin-bottom: 15px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .info-item {
            padding: 10px 15px;
            background: #F8FAFC;
            border-radius: 8px;
        }

        .info-item .label {
            font-size: 12px;
            color: #64748B;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .info-item .value {
            font-size: 14px;
            color: #0F172A;
            font-weight: 700;
        }

        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .items-table thead th {
            background: #0F1E3C;
            color: white;
            padding: 12px;
            text-align: right;
            font-size: 13px;
        }

        .items-table tbody td {
            padding: 12px;
            border-bottom: 1px solid #E2E8F0;
            font-size: 13px;
        }

        .items-table tbody tr:nth-child(even) { background: #F8FAFC; }

        /* Totals */
        .totals {
            width: 50%;
            margin-right: auto;
            margin-top: 20px;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            border-bottom: 1px solid #E2E8F0;
        }

        .totals-row.grand {
            background: linear-gradient(135deg, #D4A24C, #B8873A);
            color: white;
            font-weight: 800;
            font-size: 18px;
            border-radius: 8px;
            border: none;
            margin-top: 5px;
        }

        /* Thanks */
        .thanks {
            margin-top: 30px;
            padding: 15px;
            background: #F0F9FF;
            border-right: 4px solid #3B82F6;
            border-radius: 8px;
            font-size: 13px;
            color: #1E40AF;
        }

        /* Footer */
        .footer {
            text-align: center;
            font-size: 11px;
            color: #94A3B8;
            border-top: 1px solid #E2E8F0;
            padding-top: 15px;
            margin-top: 30px;
        }

        /* Print Button */
        .actions {
            max-width: 800px;
            margin: 0 auto 20px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            border: none;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Cairo', sans-serif;
        }

        .btn-print {
            background: linear-gradient(135deg, #D4A24C, #B8873A);
            color: white;
        }

        .btn-close {
            background: #E2E8F0;
            color: #334155;
        }

        /* Print Styles */
        @media print {
            body { background: white; padding: 0; }
            .invoice-container { box-shadow: none; padding: 20px; border-radius: 0; }
            .actions { display: none; }
            @page { margin: 15mm; size: A4; }
        }
    </style>
</head>
<body>

    {{-- أزرار (تختفي عند الطباعة) --}}
    <div class="actions">
        <button onclick="window.print()" class="btn btn-print">
            🖨️ طباعة / حفظ PDF
        </button>
        <button onclick="window.close()" class="btn btn-close">
            ✖ إغلاق
        </button>
    </div>

    <div class="invoice-container">

        {{-- Header --}}
        <div class="header">
            <div>
                <h1>🧾 فاتورة</h1>
                <p class="subtitle">{{ $siteName }} — منصة الخدمات المنزلية</p>
            </div>
            <div style="text-align: left;">
                <div class="invoice-badge">#{{ $invoiceNumber }}</div>
                <div class="invoice-meta">
                    <p>التاريخ: <strong>{{ $invoiceDate->format('Y-m-d') }}</strong></p>
                    <p>الوقت: <strong>{{ $invoiceDate->format('H:i') }}</strong></p>
                    @php
                        $statusMap = [
                            'pending'     => ['قيد الانتظار', 'status-pending'],
                            'accepted'    => ['مقبول ومدفوع', 'status-accepted'],
                            'in_progress' => ['قيد التنفيذ', 'status-progress'],
                            'completed'   => ['مكتمل', 'status-completed'],
                            'cancelled'   => ['ملغي', 'status-cancelled'],
                        ];
                        $status = $statusMap[$request->status] ?? ['غير معروف', 'status-pending'];
                    @endphp
                    <div class="status-badge {{ $status[1] }}">{{ $status[0] }}</div>
                </div>
            </div>
        </div>

        {{-- Client Info --}}
        <div class="section">
            <div class="section-title">👤 بيانات العميل</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">الاسم</div>
                    <div class="value">{{ $request->client->full_name ?? 'غير محدد' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">رقم الهاتف</div>
                    <div class="value" dir="ltr">{{ $request->client->phone ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">البريد الإلكتروني</div>
                    <div class="value" dir="ltr">{{ $request->client->email ?? '-' }}</div>
                </div>
                <div class="info-item">
                    <div class="label">عنوان الخدمة</div>
                    <div class="value">{{ $request->address }}</div>
                </div>
            </div>
        </div>

        {{-- Craftsman Info --}}
        @if($request->craftsman)
            <div class="section">
                <div class="section-title">🔧 بيانات الحرفي</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">الاسم</div>
                        <div class="value">{{ $request->craftsman->full_name }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">التخصص</div>
                        <div class="value">{{ $request->category->name ?? 'غير محدد' }}</div>
                    </div>
                    <div class="info-item">
                        <div class="label">رقم الهاتف</div>
                        <div class="value" dir="ltr">{{ $request->craftsman->phone }}</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Order Details --}}
        <div class="section">
            <div class="section-title">📋 تفاصيل الطلب</div>
            <div class="info-grid">
                <div class="info-item">
                    <div class="label">رقم الطلب</div>
                    <div class="value">#{{ str_pad($request->id, 4, '0', STR_PAD_LEFT) }}</div>
                </div>
                <div class="info-item">
                    <div class="label">التصنيف</div>
                    <div class="value">{{ $request->category->name ?? 'غير محدد' }}</div>
                </div>
                <div class="info-item" style="grid-column: span 2;">
                    <div class="label">الخدمة</div>
                    <div class="value">{{ $request->title }}</div>
                </div>
                <div class="info-item">
                    <div class="label">التاريخ المفضل</div>
                    <div class="value">{{ $request->preferred_date?->format('Y-m-d') ?? '-' }}</div>
                </div>
                @if($request->preferred_time)
                    <div class="info-item">
                        <div class="label">الوقت المفضل</div>
                        <div class="value">{{ $request->preferred_time }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Items Table --}}
        <div class="section">
            <div class="section-title">💰 تفاصيل الدفع</div>
            <table class="items-table">
                <thead>
                    <tr>
                        <th style="width: 50%;">الوصف</th>
                        <th style="width: 25%; text-align: center;">الكمية</th>
                        <th style="width: 25%; text-align: left;">المبلغ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>{{ $request->title }}</strong><br>
                            <span style="color: #64748B; font-size: 12px;">
                                {{ \Illuminate\Support\Str::limit($request->description, 100) }}
                            </span>
                        </td>
                        <td style="text-align: center;">1</td>
                        <td style="text-align: left;">{{ number_format($request->budget ?? 0) }} {{ $currency }}</td>
                    </tr>
                    @if($request->is_emergency)
                        <tr>
                            <td>
                                <strong>🚨 رسوم الطوارئ</strong><br>
                                <span style="color: #64748B; font-size: 12px;">خدمة عاجلة خلال ساعة</span>
                            </td>
                            <td style="text-align: center;">1</td>
                            <td style="text-align: left;">2,000 {{ $currency }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>

            <div class="totals">
                <div class="totals-row">
                    <span>المجموع الفرعي</span>
                    <span>{{ number_format($request->budget ?? 0) }} {{ $currency }}</span>
                </div>
                <div class="totals-row">
                    <span>الضريبة</span>
                    <span>0 {{ $currency }}</span>
                </div>
                <div class="totals-row grand">
                    <span>الإجمالي</span>
                    <span>{{ number_format($request->budget ?? 0) }} {{ $currency }}</span>
                </div>
            </div>
        </div>

        {{-- Warranty --}}
        @if($request->has_warranty && $request->warranty_end_date)
            <div class="section">
                <div class="section-title">🛡️ الضمان</div>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">تاريخ نهاية الضمان</div>
                        <div class="value">{{ $request->warranty_end_date->format('Y-m-d') }}</div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Thanks --}}
        <div class="thanks">
            <strong>شكراً لثقتك بـ {{ $siteName }}!</strong><br>
            هذه الفاتورة صادرة إلكترونياً ولا تحتاج إلى ختم أو توقيع.
            لأي استفسار، يمكنك التواصل عبر الموقع: {{ $siteUrl }}
        </div>

        {{-- Footer --}}
        <div class="footer">
            <strong>{{ $siteName }}</strong> © {{ date('Y') }} — جميع الحقوق محفوظة
        </div>

    </div>

    {{-- Auto print on load (اختياري) --}}
    @if(request()->boolean('print'))
        <script>window.onload = () => window.print();</script>
    @endif

</body>
</html>
