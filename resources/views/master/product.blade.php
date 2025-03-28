@extends('layout.main')
@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Customer Table</h6>
        </div>
        <div class="card-body">
            <div class="float-right mb-4">
                <button href="#" class="btn btn-primary text-end" onclick="add()"><i class="fa fa-plus"></i>
                    Tambah Product</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-product" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Harga Sewa perhari</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal" id="modal-product" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Customer</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="customer_form">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="product_category">Category</label>
                                    <select name="product_category" id="category-select2" class="form-select select2" style="width: 100%;">
                                        <option value="" disabled selected>Select a category</option>
                                    </select>
                                    <small id="error_category" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="product_name">Name</label>
                                    <input name="product_name" type="text" class="form-control" id="product_name" aria-describedby="emailHelp" placeholder="Enter Name">
                                    <small id="error_name" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="product_price">Harga</label>
                                    <input name="product_price" type="text" class="form-control" id="product_price" aria-describedby="emailHelp" placeholder="Enter Price">
                                    <small id="error_price" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary add-customer">Simpan</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection


@section('js_script')
<script>
    var table;
    var modal = $('#modal-category');
    var formData = $('#customer_form');
    var saveData;
    var id_category;
    var url, method;


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        loadData();
        getCategory();
        $('#error_name').css('visibility', 'hidden');
        $('#error_category').css('visibility', 'hidden');
        $('#error_price').css('visibility', 'hidden');
    });

    function loadData() {

        $('#table-product').DataTable({
            bDestroy: true,
            searching: true,
            processing: true,
            pagination: true,
            responsive: true,
            ordering: true,
            serverSide: true,
            ajax: "{{ route('data.product') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
                {
                    data: 'category.name',
                    name: 'category'
                },
                {
                    data: 'harga',
                    name: 'harga'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ]
        });
    }

    function getCategory(category_id = null) {
        let url = "{{ route('data.category.all')}}"; // Ambil semua kategori

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // console.log(response);
                // if (!response.data) return console.error('Invalid response format:', response);

                let html = '<option value="">Silahkan Pilih Category</option>';
                response.data.forEach(category => {
                    let selected = (category_id && category.uuid == category_id) ? ' selected' : '';
                    html += `<option value="${category.uuid}"${selected}>${category.name}</option>`;
                });

                $("#category-select2").html(html).select2({
                    placeholder: "Pilih Category",
                    allowClear: true,
                    width: "100%"
                });
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });
    }


    function add() {
        saveData = 'add';
        $('#modal-product').modal('show');
        formData[0].reset();
        $(".modal-title").text("Tambah Product");
        $(".add-customer").text("Tambah");
    }

    function byid(id) {

        var uuid = id;
        id_customer = id;
        saveData = 'edit';

        $('#modal-product').modal('show');
        $(".modal-title").text("Update Product");
        $(".add-product").text("Update");

        $.ajax({
            url: "{{ route('products.show', ':uuid') }}".replace(':uuid', uuid),
            method: 'get',
            dataType: "json",
            data: formData,
            success: function(response) {
                console.log(response);
                $("#product_name").val(response.data.name);
                $("#product_price").val(response.data.harga);
                getCategory(response.data.category_id);
            },
            error: function(response) {

                console.log(response);

                Swal.fire({
                    title: "Ambil" + " Data Gagal",
                    icon: "error"
                });

            }
        });

    }

    function destroy(id) {
        saveData = "delete";

        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {

                if (saveData == 'delete') {
                    url = "{{ route('products.destroy', ':uuid') }}";
                    url = url.replace(':uuid', id);
                    method = 'DELETE';
                }

                if (saveData == 'delete') {
                    formData.append('_method', 'DELETE');
                }

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: {
                        _method: 'DELETE', // Simulasi DELETE
                    },
                    success: function(response) {
                        console.log(response);
                        $('#modal-product').hide();
                        $('#modal-product').modal('hide');
                        loadData();
                        Swal.fire({
                            title: saveData + " Data Berhasil",
                            icon: "success"
                        });
                    },
                    error: function(response) {

                        console.log(response);

                        Swal.fire({
                            title: saveData + " Data Gagal",
                            icon: "error"
                        });

                    }
                });
            }
        });

    }

    $(formData).submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        if (saveData == 'add') {
            method = 'POST';
            url = "{{ route('products.store') }}";
        } else if (saveData == 'edit') {
            url = "{{ route('products.update', ':uuid') }}";
            url = url.replace(':uuid', id_customer);
            method = 'PUT';
        } else if (saveData == 'delete') {
            url = "{{ route('products.update', ':uuid') }}";
            url = url.replace(':uuid', id_customer);
            method = 'DELETE';
        }

        if (saveData == 'edit') {
            formData.append('_method', 'PUT');
        } else if (saveData == 'delete') {
            formData.append('_method', 'DELETE');
        }


        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                console.log(response);
                $('#modal-product').hide();
                $('#modal-product').modal('hide');
                loadData();
                Swal.fire({
                    title: saveData + " Data Berhasil",
                    icon: "success"
                });
            },
            error: function(response) {

                console.log(response);

                if (response.responseJSON.errors.product_name != undefined) {
                    $('#error_name').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.product_category != undefined) {
                    $('#error_email').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.product_price != undefined) {
                    $('#error_price').css('visibility', 'visible');
                }

                Swal.fire({
                    title: saveData + " Data Gagal",
                    icon: "error"
                });

                $("#error_name").html(response.responseJSON.errors.product_name);
                $("#error_email").html(response.responseJSON.errors.product_category);


            }
        });

    });
</script>
@endsection