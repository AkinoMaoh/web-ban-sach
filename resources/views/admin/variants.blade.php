@extends('admin.layout')

@section('admin_content')

<div class="container-fluid">

    <!-- =====================================================
         HEADER
    ====================================================== -->

    <div class="d-sm-flex align-items-center justify-content-between mb-4">

        <h1 class="h3 mb-0 text-gray-800">
            Trang quản lý biến thể
        </h1>

        <button type="button"
                class="btn btn-primary btn-sm shadow-sm"
                onclick="openAddModal()">

            <i class="fas fa-plus fa-sm text-white-50"></i>

            Thêm biến thể mới

        </button>

    </div>


    <!-- =====================================================
         THÔNG BÁO THÀNH CÔNG
    ====================================================== -->

    @if(session('success'))

        <div class="alert alert-success alert-dismissible fade show">

            <i class="fas fa-check-circle mr-2"></i>

            {{ session('success') }}

            <button type="button"
                    class="close"
                    data-dismiss="alert">

                <span>&times;</span>

            </button>

        </div>

    @endif


    <!-- =====================================================
         THÔNG BÁO LỖI
    ====================================================== -->

    @if(session('error'))

        <div class="alert alert-danger alert-dismissible fade show">

            <i class="fas fa-exclamation-circle mr-2"></i>

            {{ session('error') }}

            <button type="button"
                    class="close"
                    data-dismiss="alert">

                <span>&times;</span>

            </button>

        </div>

    @endif


    <!-- =====================================================
         CARD
    ====================================================== -->

    <div class="card shadow mb-4">

        <!-- HEADER -->

        <div class="card-header py-3 d-flex align-items-center justify-content-between">

            <h6 class="m-0 font-weight-bold text-primary">

                <i class="fas fa-cubes mr-2"></i>

                Dữ liệu biến thể

            </h6>


            <!-- SEARCH -->

            <div class="d-flex">

                <input type="text"
                       id="variantSearch"
                       class="form-control form-control-sm mr-2"
                       style="width:220px;"
                       placeholder="Nhập tên biến thể hoặc ID...">

                <button type="button"
                        class="btn btn-primary btn-sm"
                        onclick="searchVariant()">

                    <i class="fas fa-search"></i>

                </button>

            </div>

        </div>


        <!-- =================================================
             TABLE
        ================================================== -->

        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover"
                       id="variantTable"
                       width="100%"
                       cellspacing="0">

                    <thead class="thead-light">

                        <tr>

                            <th width="100">
                                ID
                            </th>

                            <th>
                                TÊN BIẾN THỂ
                            </th>

                            <th width="180"
                                class="text-center">

                                HÀNH ĐỘNG

                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($variants as $variant)

                            <tr class="variant-row">

                                <!-- ID -->

                                <td class="font-weight-bold text-primary">

                                    #{{ $variant['id'] }}

                                </td>


                                <!-- NAME -->

                                <td class="font-weight-bold">

                                    {{ $variant['name'] }}

                                </td>


                                <!-- ACTION -->

                                <td class="text-center">

                                    <!-- =====================
                                         SỬA
                                    ====================== -->

                                    <button type="button"
                                            class="btn btn-success btn-sm"
                                            title="Sửa biến thể"
                                            onclick="openEditModal(
                                                {{ $variant['id'] }},
                                                @js($variant['name'])
                                            )">

                                        <i class="fas fa-edit"></i>

                                    </button>


                                    <!-- =====================
                                         XÓA
                                    ====================== -->

                                    <form action="{{ route('admin.variants.destroy', $variant['id']) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirmDeleteVariant()">

                                        @csrf

                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-danger btn-sm"
                                                title="Xóa biến thể">

                                            <i class="fas fa-trash"></i>

                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="3"
                                    class="text-center text-muted py-4">

                                    <i class="fas fa-inbox fa-2x mb-2"></i>

                                    <br>

                                    Chưa có biến thể nào.

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>


<!-- =====================================================
     MODAL THÊM
====================================================== -->

<div class="modal fade"
     id="addVariantModal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog"
         role="document">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <h5 class="modal-title font-weight-bold">

                    Thêm biến thể

                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <!-- FORM -->

            <form action="{{ route('admin.variants.store') }}"
                  method="POST">

                @csrf


                <div class="modal-body">

                    <div class="form-group">

                        <label class="font-weight-bold">

                            Tên biến thể

                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               placeholder="Nhập tên biến thể..."
                               maxlength="255"
                               autocomplete="off"
                               required>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Hủy

                    </button>


                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>

                        Lưu

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<!-- =====================================================
     MODAL SỬA
====================================================== -->

<div class="modal fade"
     id="editVariantModal"
     tabindex="-1"
     role="dialog"
     aria-hidden="true">

    <div class="modal-dialog"
         role="document">

        <div class="modal-content">


            <!-- HEADER -->

            <div class="modal-header">

                <h5 class="modal-title font-weight-bold">

                    Sửa biến thể

                </h5>

                <button type="button"
                        class="close"
                        data-dismiss="modal">

                    <span>&times;</span>

                </button>

            </div>


            <!-- FORM -->

            <form id="editVariantForm"
                  method="POST">

                @csrf

                @method('PUT')


                <div class="modal-body">

                    <!-- ID -->

                    <div class="form-group">

                        <label class="font-weight-bold">

                            ID

                        </label>

                        <input type="text"
                               id="editVariantId"
                               class="form-control"
                               readonly>

                    </div>


                    <!-- NAME -->

                    <div class="form-group">

                        <label class="font-weight-bold">

                            Tên biến thể

                        </label>

                        <input type="text"
                               name="name"
                               id="editVariantName"
                               class="form-control"
                               maxlength="255"
                               autocomplete="off"
                               required>

                    </div>

                </div>


                <!-- FOOTER -->

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Hủy

                    </button>


                    <button type="submit"
                            class="btn btn-primary">

                        <i class="fas fa-save mr-1"></i>

                        Lưu thay đổi

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


<script>

/*
|--------------------------------------------------------------------------
| MỞ MODAL THÊM
|--------------------------------------------------------------------------
*/

function openAddModal()
{
    $('#addVariantModal').modal('show');

    setTimeout(function () {

        $('#addVariantModal input[name="name"]').focus();

    }, 300);
}


/*
|--------------------------------------------------------------------------
| MỞ MODAL SỬA
|--------------------------------------------------------------------------
*/

function openEditModal(id, name)
{
    const idInput =
        document.getElementById('editVariantId');

    const nameInput =
        document.getElementById('editVariantName');

    const form =
        document.getElementById('editVariantForm');


    // ID
    idInput.value = id;


    // Tên
    nameInput.value = name || '';


    // URL cập nhật
    form.action =
        "{{ url('/admin/variants') }}/" + id;


    // Mở modal
    $('#editVariantModal').modal('show');


    // Focus
    setTimeout(function () {

        nameInput.focus();

        nameInput.setSelectionRange(
            nameInput.value.length,
            nameInput.value.length
        );

    }, 300);
}


/*
|--------------------------------------------------------------------------
| TÌM KIẾM
|--------------------------------------------------------------------------
*/

function searchVariant()
{
    const input =
        document.getElementById('variantSearch');


    if (!input) {
        return;
    }


    const keyword =
        input.value
            .toLowerCase()
            .trim();


    const rows =
        document.querySelectorAll('.variant-row');


    rows.forEach(function (row) {

        const idCell =
            row.querySelector('td:nth-child(1)');


        const nameCell =
            row.querySelector('td:nth-child(2)');


        if (!idCell || !nameCell) {
            return;
        }


        const id =
            idCell.innerText
                .toLowerCase()
                .trim();


        const name =
            nameCell.innerText
                .toLowerCase()
                .trim();


        if (
            keyword === '' ||
            id.includes(keyword) ||
            name.includes(keyword)
        ) {

            row.style.display = '';

        } else {

            row.style.display = 'none';

        }

    });
}


/*
|--------------------------------------------------------------------------
| ENTER TÌM KIẾM
|--------------------------------------------------------------------------
*/

document.addEventListener(
    'DOMContentLoaded',
    function ()
    {
        const input =
            document.getElementById('variantSearch');


        if (!input) {
            return;
        }


        input.addEventListener(
            'keydown',
            function (event)
            {
                if (event.key === 'Enter') {

                    event.preventDefault();

                    searchVariant();

                }
            }
        );
    }
);


/*
|--------------------------------------------------------------------------
| XÁC NHẬN XÓA
|--------------------------------------------------------------------------
*/

function confirmDeleteVariant()
{
    return confirm(
        'Bạn có chắc muốn xóa biến thể này không?\n\n' +
        'Nếu biến thể đang được sử dụng cho sản phẩm, hệ thống sẽ không cho phép xóa.'
    );
}

</script>

@endsection