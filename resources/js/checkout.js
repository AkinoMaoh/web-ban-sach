import axios from 'axios';

document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('checkoutForm');

    if (!form) {
        return;
    }

    const draftKey = 'web-ban-sach:checkout-draft:v2';
    const csrf = form.dataset.csrf;
    const checkoutToken = document.getElementById('checkout_token');
    const province = document.getElementById('province');
    const district = document.getElementById('district');
    const ward = document.getElementById('ward');
    const savedAddress = document.getElementById('saved_address');
    const addressId = document.getElementById('address_id');
    const quoteToken = document.getElementById('shipping_quote_token');
    const shippingText = document.getElementById('shipping_fee_text');
    const voucherCode = document.getElementById('voucher_code');
    const appliedVoucher = document.getElementById('applied_voucher_input');
    const voucherMessage = document.getElementById('voucher-message');
    const discountRow = document.getElementById('discount-row');
    const discountText = document.getElementById('discount_amount_text');
    const submitButton = document.getElementById('checkout-submit');
    const submitLabel = submitButton.querySelector('.submit-label');
    const submitLoading = submitButton.querySelector('.submit-loading');
    const saveAddress = document.getElementById('save_address');
    const setDefaultAddress = document.getElementById('set_default_address');
    const defaultAddressWrap = document.getElementById('default_address_wrap');
    const subtotal = Number(document.getElementById('subtotal_text').dataset.value) || 0;
    let shippingFee = 0;
    let discountAmount = 0;
    let shippingRequest = 0;
    let submitting = false;
    let draftTimer;

    const money = (value) => `${new Intl.NumberFormat('vi-VN').format(Math.max(Number(value) || 0, 0))}đ`;

    function calculateTotal() {
        document.getElementById('total_amount_text').textContent = money(
            Math.max(subtotal + shippingFee - discountAmount, 0)
        );
    }

    function draftValue(id) {
        const element = document.getElementById(id);
        return element ? element.value : '';
    }

    function saveDraftNow() {
        const payment = form.querySelector('input[name="payment_method"]:checked');
        const draft = {
            shipping_name: draftValue('shipping_name'),
            billing_email: draftValue('billing_email'),
            shipping_phone: draftValue('shipping_phone'),
            province_id: province.value,
            district_id: district.value,
            ward_code: ward.value,
            specific_address: draftValue('specific_address'),
            order_notes: draftValue('order_notes'),
            payment_method: payment?.value || 'cod',
            voucher_code: voucherCode.value,
            address_id: addressId.value,
            save_address: Boolean(saveAddress?.checked),
            set_default_address: Boolean(setDefaultAddress?.checked),
            agree_terms: Boolean(document.getElementById('agree_terms')?.checked),
            checkout_token: checkoutToken.value,
        };

        try {
            sessionStorage.setItem(draftKey, JSON.stringify(draft));
        } catch (error) {
            // Trình duyệt có thể chặn storage; form vẫn hoạt động bằng old() của Laravel.
        }
    }

    function queueDraftSave() {
        window.clearTimeout(draftTimer);
        draftTimer = window.setTimeout(saveDraftNow, 180);
    }

    function readDraft() {
        if (form.dataset.hasOldInput === '1') {
            return null;
        }

        try {
            return JSON.parse(sessionStorage.getItem(draftKey) || 'null');
        } catch (error) {
            return null;
        }
    }

    function restoreDraft(draft) {
        if (!draft) {
            return;
        }

        [
            'shipping_name',
            'billing_email',
            'shipping_phone',
            'specific_address',
            'order_notes',
        ].forEach((id) => {
            const element = document.getElementById(id);
            if (element && typeof draft[id] === 'string') {
                element.value = draft[id];
            }
        });

        province.value = draft.province_id || '';
        district.dataset.selected = draft.district_id || '';
        ward.dataset.selected = draft.ward_code || '';
        voucherCode.value = draft.voucher_code || '';
        addressId.value = draft.address_id || '';

        if (typeof draft.checkout_token === 'string' && draft.checkout_token !== '') {
            checkoutToken.value = draft.checkout_token;
        }

        if (savedAddress) {
            savedAddress.value = draft.address_id || '';
        }

        const payment = [...form.querySelectorAll('input[name="payment_method"]')]
            .find((input) => input.value === (draft.payment_method || 'cod'));
        if (payment) payment.checked = true;
        if (saveAddress) saveAddress.checked = Boolean(draft.save_address);
        if (setDefaultAddress) setDefaultAddress.checked = Boolean(draft.set_default_address);

        const agreeTerms = document.getElementById('agree_terms');
        if (agreeTerms) agreeTerms.checked = Boolean(draft.agree_terms);
    }

    function replaceOptions(select, placeholder, rows, valueKey, selectedValue) {
        select.replaceChildren();
        select.add(new Option(placeholder, ''));

        rows.forEach((row) => {
            const option = new Option(row.name, String(row[valueKey]));
            option.selected = String(row[valueKey]) === String(selectedValue || '');
            select.add(option);
        });
    }

    async function loadWards(districtId, selectedWard = '') {
        replaceOptions(ward, 'Đang tải...', [], 'code', '');

        if (!districtId) {
            replaceOptions(ward, 'Chọn Phường/Xã', [], 'code', '');
            return;
        }

        try {
            const response = await axios.get(`${form.dataset.wardUrl}/${encodeURIComponent(districtId)}`);
            replaceOptions(ward, 'Chọn Phường/Xã', response.data.data || response.data, 'code', selectedWard);

            if (ward.value) {
                await calculateShipping();
            }
        } catch (error) {
            replaceOptions(ward, 'Không thể tải Phường/Xã', [], 'code', '');
        }
    }

    async function loadDistricts(provinceId, selectedDistrict = '', selectedWard = '') {
        replaceOptions(district, 'Đang tải...', [], 'id', '');
        replaceOptions(ward, 'Chọn Phường/Xã', [], 'code', '');

        if (!provinceId) {
            replaceOptions(district, 'Chọn Quận/Huyện', [], 'id', '');
            return;
        }

        try {
            const response = await axios.get(`${form.dataset.districtUrl}/${encodeURIComponent(provinceId)}`);
            replaceOptions(district, 'Chọn Quận/Huyện', response.data.data || response.data, 'id', selectedDistrict);

            if (district.value) {
                await loadWards(district.value, selectedWard);
            }
        } catch (error) {
            replaceOptions(district, 'Không thể tải Quận/Huyện', [], 'id', '');
        }
    }

    function resetShipping(message = 'Vui lòng chọn địa chỉ') {
        shippingRequest += 1;
        shippingFee = 0;
        quoteToken.value = '';
        shippingText.textContent = message;
        shippingText.classList.remove('text-danger');
        calculateTotal();
    }

    async function calculateShipping() {
        if (!province.value || !district.value || !ward.value) {
            resetShipping();
            return;
        }

        const requestId = ++shippingRequest;
        quoteToken.value = '';
        shippingText.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Đang tính phí...';

        try {
            const response = await axios.post(form.dataset.feeUrl, {
                _token: csrf,
                province_id: province.value,
                district_id: district.value,
                ward_code: ward.value,
            });

            if (requestId !== shippingRequest) return;

            shippingFee = Number(response.data.fee) || 0;
            quoteToken.value = response.data.quote_token || '';
            shippingText.textContent = money(shippingFee);
            shippingText.classList.remove('text-danger');
            calculateTotal();
            queueDraftSave();
        } catch (error) {
            if (requestId !== shippingRequest) return;

            const message = error.response?.data?.message || 'Không thể tính phí, vui lòng thử lại.';
            resetShipping(message);
            shippingText.classList.add('text-danger');
        }
    }

    function resetVoucher(message = '', clearCode = false) {
        discountAmount = 0;
        appliedVoucher.value = '';
        discountRow.classList.add('d-none');
        discountText.textContent = '-0đ';
        document.getElementById('btn-remove-voucher').classList.add('d-none');

        if (clearCode) voucherCode.value = '';

        voucherMessage.textContent = message;
        voucherMessage.className = message ? 'd-block font-weight-bold text-danger' : 'd-block font-weight-bold';
        calculateTotal();
        queueDraftSave();
    }

    async function applyVoucher() {
        const code = voucherCode.value.trim().toUpperCase();
        const button = document.getElementById('btn-apply-voucher');

        if (!code) {
            resetVoucher('Vui lòng nhập mã giảm giá.');
            return;
        }

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Đang kiểm tra';

        try {
            const response = await axios.post(form.dataset.voucherUrl, {
                _token: csrf,
                voucher_code: code,
                billing_email: draftValue('billing_email'),
            });

            discountAmount = Number(response.data.discount_amount) || 0;
            appliedVoucher.value = response.data.voucher_code;
            voucherCode.value = response.data.voucher_code;
            discountText.textContent = `-${money(discountAmount)}`;
            discountRow.classList.remove('d-none');
            document.getElementById('btn-remove-voucher').classList.remove('d-none');
            voucherMessage.textContent = response.data.message;
            voucherMessage.className = 'd-block font-weight-bold text-success';
            calculateTotal();
            queueDraftSave();
        } catch (error) {
            const errors = error.response?.data?.errors;
            const firstError = errors ? Object.values(errors).flat()[0] : null;
            resetVoucher(error.response?.data?.message || firstError || 'Không thể kiểm tra voucher.');
        } finally {
            button.disabled = false;
            button.textContent = 'Áp dụng';
        }
    }

    function toggleDefaultAddress() {
        if (!saveAddress || !defaultAddressWrap) return;
        defaultAddressWrap.classList.toggle('d-none', !saveAddress.checked);

        if (!saveAddress.checked && setDefaultAddress) {
            setDefaultAddress.checked = false;
        }
    }

    const restoredDraft = readDraft();

    if (!restoredDraft && form.dataset.hasOldInput !== '1' && window.crypto?.randomUUID) {
        checkoutToken.value = window.crypto.randomUUID();
    }

    restoreDraft(restoredDraft);
    toggleDefaultAddress();

    const initialDistrict = district.dataset.selected || '';
    const initialWard = ward.dataset.selected || '';
    if (province.value) {
        loadDistricts(province.value, initialDistrict, initialWard);
    }

    province.addEventListener('change', () => {
        addressId.value = '';
        resetShipping();
        loadDistricts(province.value);
        queueDraftSave();
    });

    district.addEventListener('change', () => {
        addressId.value = '';
        resetShipping();
        loadWards(district.value);
        queueDraftSave();
    });

    ward.addEventListener('change', () => {
        addressId.value = '';
        resetShipping();
        calculateShipping();
        queueDraftSave();
    });

    savedAddress?.addEventListener('change', async () => {
        const option = savedAddress.selectedOptions[0];
        addressId.value = option?.value || '';

        if (!option?.value) {
            queueDraftSave();
            return;
        }

        document.getElementById('shipping_name').value = option.dataset.name || '';
        document.getElementById('shipping_phone').value = option.dataset.phone || '';
        document.getElementById('specific_address').value = option.dataset.street || '';
        province.value = option.dataset.province || '';
        resetShipping();
        await loadDistricts(province.value, option.dataset.district, option.dataset.ward);
        queueDraftSave();
    });

    ['shipping_name', 'shipping_phone', 'specific_address'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', () => {
            if (savedAddress && addressId.value) {
                addressId.value = '';
                savedAddress.value = '';
            }
            queueDraftSave();
        });
    });

    ['billing_email', 'order_notes', 'agree_terms'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', queueDraftSave);
        document.getElementById(id)?.addEventListener('change', queueDraftSave);
    });

    form.querySelectorAll('input[name="payment_method"]').forEach((radio) => {
        radio.addEventListener('change', queueDraftSave);
    });

    saveAddress?.addEventListener('change', () => {
        toggleDefaultAddress();
        queueDraftSave();
    });
    setDefaultAddress?.addEventListener('change', () => {
        if (setDefaultAddress.checked && saveAddress) saveAddress.checked = true;
        toggleDefaultAddress();
        queueDraftSave();
    });

    voucherCode.addEventListener('input', () => {
        voucherCode.value = voucherCode.value.toUpperCase().replace(/[^A-Z0-9_-]/g, '');
        queueDraftSave();
    });
    voucherCode.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            applyVoucher();
        }
    });

    document.getElementById('btn-apply-voucher').addEventListener('click', applyVoucher);
    document.getElementById('btn-remove-voucher').addEventListener('click', () => {
        resetVoucher('', true);
        voucherMessage.textContent = 'Đã bỏ mã giảm giá.';
        voucherMessage.className = 'd-block font-weight-bold text-muted';
    });

    document.querySelectorAll('.btn-select-voucher').forEach((button) => {
        button.addEventListener('click', () => {
            voucherCode.value = button.dataset.code || '';
            window.jQuery?.('#voucherModal').modal('hide');
            applyVoucher();
        });
    });

    document.getElementById('billing_email').addEventListener('change', () => {
        if (appliedVoucher.value) {
            resetVoucher('Email đã thay đổi, vui lòng áp dụng lại voucher.');
        }
    });

    if (voucherCode.value) {
        applyVoucher();
    }

    form.addEventListener('submit', (event) => {
        saveDraftNow();

        if (!quoteToken.value) {
            event.preventDefault();
            shippingText.textContent = 'Vui lòng chọn đủ địa chỉ và chờ tính phí vận chuyển.';
            shippingText.classList.add('text-danger');
            ward.focus();
            return;
        }

        if (submitting) {
            event.preventDefault();
            return;
        }

        submitting = true;
        submitButton.disabled = true;
        submitLabel.classList.add('d-none');
        submitLoading.classList.remove('d-none');
    });

    window.addEventListener('pageshow', () => {
        submitting = false;
        submitButton.disabled = false;
        submitLabel.classList.remove('d-none');
        submitLoading.classList.add('d-none');
    });
});
