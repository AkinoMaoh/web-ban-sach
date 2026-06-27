@extends('admin.layout')

@section('admin_content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Tổng Quan Thống Kê</h1>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Doanh Thu (Hôm nay)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($doanhThuHomNay, 0, ',', '.') }} đ</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-calendar-day fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Doanh Thu (Tháng)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($doanhThuThangNay, 0, ',', '.') }} đ</div>
                        <div class="mt-2 small {{ $tangTruong >= 0 ? 'text-success' : 'text-danger' }}">
                            <i class="fas {{ $tangTruong >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                            {{ abs($tangTruong) }}% so với tháng trước
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-dollar-sign fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Khách Mới (Tháng)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $khachMoiThang }} User</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-users fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Đánh giá / Bình luận</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $phanHoiMoi }}</div>
                    </div>
                    <div class="col-auto"><i class="fas fa-comments fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-3 col-md-6 mb-2">
        <a href="{{ route('admin.orders', ['status' => 'pending']) }}" class="btn btn-warning btn-block text-left shadow-sm font-weight-bold text-dark">
            <i class="fas fa-clock"></i> Chờ xác nhận: <span class="badge badge-light float-right mt-1">{{ $donMoi }}</span>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 mb-2">
        <a href="{{ route('admin.orders', ['status' => 'shipping']) }}" class="btn btn-primary btn-block text-left shadow-sm font-weight-bold">
            <i class="fas fa-truck"></i> Đang giao: <span class="badge badge-light float-right mt-1">{{ $donDangGiao }}</span>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 mb-2">
        <a href="{{ route('admin.orders', ['status' => 'completed']) }}" class="btn btn-success btn-block text-left shadow-sm font-weight-bold">
            <i class="fas fa-check-circle"></i> Thành công: <span class="badge badge-light float-right mt-1">{{ $donThanhCong }}</span>
        </a>
    </div>
    <div class="col-lg-3 col-md-6 mb-2">
        <a href="{{ route('admin.orders', ['status' => 'cancelled']) }}" class="btn btn-danger btn-block text-left shadow-sm font-weight-bold">
            <i class="fas fa-times-circle"></i> Đã hủy: <span class="badge badge-light float-right mt-1">{{ $donDaHuy }}</span>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Doanh Thu 12 Tháng Năm {{ date('Y') }}</h6></div>
            <div class="card-body"><div class="chart-area"><canvas id="myAreaChart"></canvas></div></div>
        </div>
    </div>
    
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Tỷ Trọng Bán Chạy Theo Danh Mục</h6></div>
            <div class="card-body">
                @if(count($bieuDoDanhMuc) > 0)
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="myPieChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2"><i class="fas fa-circle text-primary"></i> Trực tiếp</span>
                        <span class="mr-2"><i class="fas fa-circle text-success"></i> Mạng xã hội</span>
                        <span class="mr-2"><i class="fas fa-circle text-info"></i> Giới thiệu</span>
                    </div>
                @else
                    <div class="d-flex align-items-center justify-content-center" style="height: 250px;">
                        <span class="text-muted"><i class="fas fa-box-open mr-2"></i>Chưa có dữ liệu đơn hàng</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-success"><h6 class="m-0 font-weight-bold text-white">🏆 Top 5 Sách Bán Chạy</h6></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($topSanPham as $sp)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $sp->name }} <span class="badge badge-success badge-pill">{{ $sp->total_sold }} cuốn</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-3">Chưa có dữ liệu</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-danger"><h6 class="m-0 font-weight-bold text-white">⚠️ Sắp Hết Hàng (< 5)</h6></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($sapHetHang as $sp)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <a href="{{ route('admin.products.edit', $sp->id) }}" class="text-danger">{{ $sp->name }}</a> 
                            <span class="badge badge-danger badge-pill">Còn {{ $sp->stock }}</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-3">Kho hàng dồi dào</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <div class="col-lg-4 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3 bg-primary"><h6 class="m-0 font-weight-bold text-white">💎 Khách Hàng VIP</h6></div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    @forelse($khachVIP as $vip)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <strong>{{ $vip->name }}</strong><br>
                                <small class="text-muted">{{ $vip->email }}</small>
                            </div>
                            <span class="badge badge-primary badge-pill">{{ number_format($vip->total_spent, 0, ',', '.') }}đ</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center py-3">Chưa có khách hàng</li>
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
        // Hàm format tiền tệ VNĐ cho đẹp
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

        // 1. VẼ BIỂU ĐỒ ĐƯỜNG (DOANH THU)
        var ctxArea = document.getElementById("myAreaChart");
        if(ctxArea) {
            new Chart(ctxArea, {
                type: 'line',
                data: {
                    labels: ["Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"],
                    datasets: [{
                        label: "Doanh thu",
                        lineTension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 3,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
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
                                maxTicksLimit: 5,
                                padding: 10,
                                callback: function(value, index, values) {
                                    return number_format(value) + ' đ'; 
                                }
                            },
                            gridLines: { color: "rgb(234, 236, 244)", zeroLineColor: "rgb(234, 236, 244)", drawBorder: false, borderDash: [2], zeroLineBorderDash: [2] }
                        }],
                    },
                    legend: { display: false },
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        titleMarginBottom: 10,
                        titleFontColor: '#6e707e',
                        titleFontSize: 14,
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                        callbacks: {
                            label: function(tooltipItem, chart) {
                                var datasetLabel = chart.datasets[tooltipItem.datasetIndex].label || '';
                                return datasetLabel + ': ' + number_format(tooltipItem.yLabel) + ' đ'; 
                            }
                        }
                    }
                }
            });
        }

        // 2. VẼ BIỂU ĐỒ TRÒN (DANH MỤC)
        var ctxPie = document.getElementById("myPieChart");
        if(ctxPie && danhMucData.length > 0) {
            new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: danhMucLabels,
                    datasets: [{
                        data: danhMucData,
                        backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#5a5c69'],
                        hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf', '#dda20a', '#be2617', '#60616f', '#373840'],
                        hoverBorderColor: "rgba(234, 236, 244, 1)",
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    tooltips: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyFontColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: { display: true, position: 'bottom' },
                    cutoutPercentage: 80,
                },
            });
        }
    });
</script>

@endsection