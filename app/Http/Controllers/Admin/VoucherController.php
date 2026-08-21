<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreVoucherRequest;
use App\Http\Requests\Admin\UpdateVoucherRequest;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoucherController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $query = $status === 'archived'
            ? Voucher::onlyTrashed()
            : Voucher::query();

        $query
            ->search($request->string('keyword')->toString())
            ->when(
                in_array($request->input('type'), ['fixed', 'percent'], true),
                fn (Builder $voucherQuery) => $voucherQuery->where('type', $request->input('type'))
            );

        $today = now()->toDateString();

        match ($status) {
            'active' => $query->available(),
            'inactive' => $query->where('is_active', false),
            'upcoming' => $query->where('is_active', true)->whereDate('start_date', '>', $today),
            'expired' => $query->whereDate('end_date', '<', $today),
            'exhausted' => $query
                ->whereNotNull('usage_limit')
                ->whereColumn('used_count', '>=', 'usage_limit'),
            default => null,
        };

        $vouchers = $query
            ->withCount([
                'usages',
                'usages as active_usages_count' => fn (Builder $usageQuery) => $usageQuery
                    ->whereIn('status', [VoucherUsage::STATUS_RESERVED, VoucherUsage::STATUS_USED]),
            ])
            ->latest('id')
            ->paginate(12)
            ->withQueryString();

        $stats = [
            'total' => Voucher::count(),
            'active' => Voucher::available()->count(),
            'upcoming' => Voucher::where('is_active', true)->whereDate('start_date', '>', $today)->count(),
            'expired' => Voucher::whereDate('end_date', '<', $today)->count(),
            'archived' => Voucher::onlyTrashed()->count(),
        ];

        return view('admin.voucher', compact('vouchers', 'stats'));
    }

    public function create(): View
    {
        return view('admin.voucherAdd');
    }

    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        $voucher = Voucher::create($request->validated());

        return redirect()
            ->route('admin.vouchers.show', $voucher)
            ->with('success', 'Thêm mã giảm giá thành công.');
    }

    public function show(Voucher $voucher): View
    {
        $voucher->loadCount([
            'usages',
            'usages as reserved_usages_count' => fn (Builder $query) => $query
                ->where('status', VoucherUsage::STATUS_RESERVED),
            'usages as completed_usages_count' => fn (Builder $query) => $query
                ->where('status', VoucherUsage::STATUS_USED),
            'usages as released_usages_count' => fn (Builder $query) => $query
                ->where('status', VoucherUsage::STATUS_RELEASED),
        ]);

        $usages = $voucher->usages()
            ->with(['order', 'user'])
            ->latest('id')
            ->paginate(15);

        return view('admin.voucherShow', compact('voucher', 'usages'));
    }

    public function edit(Voucher $voucher): View
    {
        return view('admin.voucherEdit', compact('voucher'));
    }

    public function update(UpdateVoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $voucher->update($request->validated());

        return redirect()
            ->route('admin.vouchers.show', $voucher)
            ->with('success', 'Cập nhật mã giảm giá thành công.');
    }

    public function toggle(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => ! $voucher->is_active]);

        $message = $voucher->is_active
            ? 'Đã bật mã giảm giá.'
            : 'Đã tạm khóa mã giảm giá.';

        return back()->with('success', $message);
    }

    public function destroy(Voucher $voucher): RedirectResponse
    {
        $voucher->update(['is_active' => false]);
        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', 'Đã lưu trữ mã giảm giá. Lịch sử sử dụng vẫn được giữ nguyên.');
    }

    public function restore(int $voucher): RedirectResponse
    {
        $archivedVoucher = Voucher::onlyTrashed()->findOrFail($voucher);
        $archivedVoucher->restore();
        $archivedVoucher->update(['is_active' => false]);

        return redirect()
            ->route('admin.vouchers.edit', $archivedVoucher)
            ->with('success', 'Đã khôi phục voucher ở trạng thái tạm khóa.');
    }
}
