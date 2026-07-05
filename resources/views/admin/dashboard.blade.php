@extends('admin.layout')

@section('admin_content')

<style>
    /* --- CUSTOM CSS CHO DASHBOARD --- */
    /* Ghi đè thẻ thống kê */
    .stat-card {
        border: none;
        border-radius: 12px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stat-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    
    .border-left-blue { border-left: 4px solid var(--admin-primary) !important; }
    .border-left-dark { border-left: 4px solid #5a5c69 !important; }
    .border-left-green { border-left: 4px solid #34A853 !important; }
    .border-left-purple { border-left: 4px solid #8E44AD !important; }

    /* Nút trạng thái đơn hàng */
    .order-status-btn {
        border-radius: 10px;
        border: none;
        transition: 0.3s;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
    }
    .order-status-btn:hover { transform: scale(1.02); color: #fff !important; }
    .order-status-btn .badge { font-size: 14px; padding: 6px 10px; border-radius: 6px; }

    /* Header của thẻ (Card Header) */
    .custom-card-header { background-color: #fff; border-bottom: 1px solid #f0f0f0; border-radius: 12px 12px 0 0 !important; padding: 15px 20px; }
    
    /* List Group */
    .custom-list-item { border-left: none; border-right: none; padding: 15px 20px; transition: 0.2s; }
    .custom-list-item:hover { background-color: #f8f9fa; }
    .custom-list-item:first-child { border-top: none; }
</style>

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800 serif-font font-weight-bold">Tổng Quan Thống Kê</h1>
    <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
        <i class="fas fa-download fa-sm text-white-50"></i> Xuất Báo Cáo
    </a>
</div>

<!-- 1. THẺ THỐNG KÊ DOANH THU & USER -->
<div class="row">
    <!-- Doanh thu hôm nay -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-blue shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh Thu (Hôm nay)</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($doanhThuHomNay, 0, ',', '.') }} đ</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-light p-3 rounded-circle"><i class="fas fa-wallet fa-2x text-primary"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Doanh thu tháng -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-dark shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">Doanh Thu (Tháng)</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ number_format($doanhThuThangNay, 0, ',', '.') }} đ</div>
                        <div class="mt-2 small font-weight-bold {{ $tangTruong >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas {{ $tangTruong >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($tangTruong) }}% so với tháng trước
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-light p-3 rounded-circle"><i class="fas fa-chart-line fa-2x text-secondary"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Khách mới -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-green shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Khách Mới (Tháng)</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $khachMoiThang }} User</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-light p-3 rounded-circle"><i class="fas fa-user-plus fa-2x text-success"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Phản hồi -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card stat-card border-left-purple shadow-sm h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-uppercase mb-1" style="color: #8E44AD;">Đánh giá / Phản hồi</div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">{{ $phanHoiMoi }}</div>
                    </div>
                    <div class="col-auto">
                        <div class="bg-light p-3 rounded-circle"><i class="fas fa-comments fa-2x" style="color: #8E44AD;"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 2. TRẠNG THÁI ĐƠN HÀNG NHANH -->
<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-3">
        <a href="{{ route('admin.orders', ['status' => 'pending']) }}" class="btn btn-warning order-status-btn shadow-sm text-dark font-weight-bold w-100 text-decoration-none">
            <span><i class="fas fa-clock mr-2"></i> Chờ xác nhận</span>
            <span class="badge badge-light text-dark shadow-sm">{{ $donMoi }}</span>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <a href="{{ route('admin.orders', ['status' => 'shipping']) }}" class="btn btn-info order-status-btn shadow-sm text-white font-weight-bold w-100 text-decoration-none">
            <span><i class="fas fa-truck mr-2"></i> Đang giao</span>
            <span class="badge badge-light text-info shadow-sm">{{ $donDangGiao }}</span>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <a href="{{ route('admin.orders', ['status' => 'completed']) }}" class="btn btn-success order-status-btn shadow-sm text-white font-weight-bold w-100 text-decoration-none">
            <span><i class="fas fa-check-circle mr-2"></i> Thành công</span>
            <span class="badge badge-light text-success shadow-sm">{{ $donThanhCong }}</span>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 mb-3">
        <a href="{{ route('admin.orders', ['status' => 'cancelled']) }}" class="btn btn-danger order-status-btn shadow-sm text-white font-weight-bold w-100 text-decoration-none">
            <span><i class="fas fa-times-circle mr-2"></i> Đã hủy</span>
            <span class="badge badge-light text-danger shadow-sm">{{ $donDaHuy }}</span>
        </a>
    </div>
</div>

<!-- 3. BIỂU ĐỒ (CHARTS) -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="custom-card-header d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark serif-font"><i class="fas fa-chart-area mr-2 text-primary"></i>Doanh Thu Năm {{ date('Y') }}</h6>
            </div>
            <div class="card-body">
                <div class="chart-area"><canvas id="myAreaChart"></canvas></div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px;">
            <div class="custom-card-header d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-dark serif-font"><i class="fas fa-chart-pie mr-2 text-primary"></i>Tỷ Trọng Danh Mục</h6>
            </div>
            <div class="card-body">
                @if(count($bieuDoDanhMuc) > 0)
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small text-muted">
                        Biểu đồ phân bổ doanh thu theo từng Thể loại sách
                    </div>
                @else
                    <div class="d-flex flex-column align-items-center justify-content-center text-center" style="height: 250px;">
                        <i class="fas fa-box-open fa-3x text-gray-300 mb-3"></i>
                        <span class="text-muted">Chưa có dữ liệu bán hàng</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- 4. DANH SÁCH XẾP HẠNG -->
<div class="row">
    <!-- Top 5 Bán Chạy -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="custom-card-header bg-white">
                <h6 class="m-0 font-weight-bold text-dark serif-font"><i class="fas fa-trophy mr-2 text-warning"></i>Top 5 Sách Bán Chạy</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topSanPham as $sp)
                        <li class="list-group-item custom-list-item d-flex justify-content-between align-items-center">
                            <span class="text-dark font-weight-bold" style="font-size: 14px;">{{ $sp->name }}</span> 
                            <span class="badge text-white badge-pill px-2 py-1 bg-primary">{{ $sp->total_sold }} cuốn</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">Chưa có dữ liệu</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Sắp hết hàng -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="custom-card-header bg-white">
                <h6 class="m-0 font-weight-bold text-dark serif-font"><i class="fas fa-exclamation-triangle mr-2 text-danger"></i>Sắp Hết Hàng (< 5)</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($sapHetHang as $sp)
                        <li class="list-group-item custom-list-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.products.edit', $sp->id) }}" class="text-danger font-weight-bold text-decoration-none" style="font-size: 14px;">{{ $sp->name }}</a> 
                            <span class="badge badge-danger badge-pill px-2 py-1">Còn {{ $sp->stock }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4"><i class="fas fa-check-circle text-success mr-2"></i>Kho hàng dồi dào</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <!-- Khách VIP -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px;">
            <div class="custom-card-header bg-white">
                <h6 class="m-0 font-weight-bold text-dark serif-font"><i class="fas fa-gem mr-2 text-primary"></i>Khách Hàng VIP</h6>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($khachVIP as $vip)
                        <li class="list-group-item custom-list-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong class="text-dark">{{ $vip->name }}</strong><br>
                                <small class="text-muted">{{ $vip->email }}</small>
                            </div>
                            <span class="badge badge-primary badge-pill px-2 py-1">{{ number_format($vip->total_spent, 0, ',', '.') }}đ</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-4">Chưa có dữ liệu</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
    var doanhThuData = @json($bieuDoDoanhThu);
    var danhMucLabels = @json(array_keys($bieuDoDanhMuc));
    var danhMucData = @json(array_values($bieuDoDanhMuc));

    document.addEventListener("DOMContentLoaded", function() {
        function number_format(number, decimals, dec_point, thousands_sep) {
            number = (number + '').replace(',', '').replace(' ', '');
            var n = !isFinite(+number) ? 0 : +number,
                prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
                sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
                dec = (typeof dec_point === 'undefined') ? ',' : dec_point,
                s = '',
                toFixedFix = function(n, prec) {
                    var k = Math.pow(10, prec);
                    return '' + Math.round(n * k) / k;
                };
            s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
            if (s[0].length > 3) {
                s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
            }
            if ((s[1] || '').length < prec) {
                s[1] = s[1] || '';
                s[1] += new Array(prec - s[1].length + 1).join('0');
            }
            return s.join(dec);
        }

        // 1. BIỂU ĐỒ ĐƯỜNG (DOANH THU) - Tone Xanh Dương
        var ctxArea = document.getElementById("myAreaChart");
        if(ctxArea) {
            new Chart(ctxArea, {
                type: 'line',
                data: {
                    labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
                    datasets: [{
                        label: "Doanh thu",
                        lineTension: 0.4,
                        backgroundColor: "rgba(26, 115, 232, 0.05)", /* Xanh dương nhạt */
                        borderColor: "rgba(26, 115, 232, 1)", /* Xanh dương đậm */
                        pointRadius: 4,
                        pointBackgroundColor: "rgba(26, 115, 232, 1)",
                        pointBorderColor: "#fff",
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: "rgba(26, 115, 232, 1)",
                        pointHoverBorderColor: "#fff",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: doanhThuData,
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    layout: { padding: { left: 10, right: 25, top: 25, bottom: 0 } },
                    scales: {
                        xAxes: [{ time: { unit: 'date' }, gridLines: { display: false, drawBorder: false }, ticks: { maxTicksLimit: 12 } }],
                        yAxes: [{
                            ticks: {
                                maxTicksLimit: 5, padding: 10,
                                callback: function(value) { return number_format(value) + ' đ'; }
                            },
                            gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                        }],
                    },
                    legend: { display: false },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)", bodyFontColor: "#858796", titleMarginBottom: 10, titleFontColor: '#6e707e', titleFontSize: 14,
                        borderColor: '#dddfeb', borderWidth: 1, xPadding: 15, yPadding: 15, displayColors: false, intersect: false, mode: 'index', caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                return chart.datasets[tooltipItem.datasetIndex].label + ': ' + number_format(tooltipItem.yLabel) + ' đ'; 
                            }
                        }
                    }
                }
            });
        }

        // 2. BIỂU ĐỒ TRÒN (TỶ TRỌNG DANH MỤC) - Bảng màu Lạnh/Sáng
        var ctxPie = document.getElementById("myPieChart");
        if(ctxPie && danhMucData.length > 0) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: danhMucLabels,
                    datasets: [{
                        data: danhMucData,
                        backgroundColor: ['#1A73E8', '#4285F4', '#9AA0A6', '#34A853', '#FBBC05', '#EA4335'],
                        hoverBackgroundColor: ['#1557B0', '#3367D6', '#80868B', '#248A3D', '#F4B400', '#D93025'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255, 255, 255)", bodyFontColor: "#858796", borderColor: '#dddfeb', borderWidth: 1,
                        xPadding: 15, yPadding: 15, displayColors: true, caretPadding: 10,
                    },
                    legend: { display: true, position: 'bottom' },
                    cutoutPercentage: 75,
                },
            });
        }
    });
</script>

@endsection