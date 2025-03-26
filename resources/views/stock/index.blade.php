@extends('layout.main')
@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Stock Manajemen Table</h6>
        </div>
        <div class="card-body">
            <div class="float-right mb-4">
                <button href="#" class="btn btn-primary text-end" onclick="add()"><i class="fa fa-wrench"></i>
                    Atur Stock</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-stock" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal" id="modal-stock" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Setting Stock</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="stock_form">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="product_name">Product</label>
                                    <select name="product_name" id="product-select2" class="form-select select2" style="width: 100%;">
                                        <option value="" disabled selected>Select a category</option>
                                    </select>
                                    <small id="error_product" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="product_qty">Qty</label>
                                    <input name="product_qty" type="text" class="form-control" id="product_qty" aria-describedby="emailHelp" placeholder="Enter QTY">
                                    <small id="error_qty" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="product_status">Status</label>
                                    <select name="product_status" id="status-select2" class="form-select select2" style="width: 100%;">
                                        <option value="" disabled selected>Select a Status</option>
                                    </select>
                                    <small id="error_status" class="form-text text-danger">We'll never share your email with anyone else.</small>
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
    var modal = $('#modal-stock');
    var formData = $('#stock_form');
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
        getProduct();
        getStatus();
        $('#error_product').css('visibility', 'hidden');
        $('#error_qty').css('visibility', 'hidden');
        $('#error_status').css('visibility', 'hidden');
    });

    function loadData() {

        $('#table-stock').DataTable({
            bDestroy: true,
            searching: true,
            processing: true,
            pagination: true,
            responsive: true,
            ordering: true,
            serverSide: true,
            ajax: "{{ route('data.stock') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'product.name',
                    name: 'name'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'status',
                    name: 'status'
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

    function getProduct(product_id = null) {
        let url = "{{ route('data.product.all')}}"; // Ambil semua kategori

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // console.log(response);
                // if (!response.data) return console.error('Invalid response format:', response);

                let html = '<option value="">Silahkan Pilih Product</option>';
                response.data.forEach(product => {
                    let selected = (product_id && product.uuid == product_id) ? ' selected' : '';
                    html += `<option value="${product.uuid}"${selected}>${product.name}</option>`;
                });

                $("#product-select2").html(html).select2({
                    placeholder: "Pilih Product",
                    allowClear: true,
                    width: "100%"
                });
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });
    }

    function getStatus(status_id = null) {
        let url = "{{ route('data.utility.all_status')}}"; // Ambil semua kategori

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // console.log(response);
                // if (!response.data) return console.error('Invalid response format:', response);
                let html = '<option value="">Silahkan Pilih Status</option>';

                response.data.forEach(status => {
                    let selected = (status_id && status.name == status_id) ? ' selected' : '';
                    html += `<option value="${status.name}"${selected}>${status.name}</option>`;
                });

                $("#status-select2").html(html).select2({
                    placeholder: "Pilih Status",
                    allowClear: true,
                    width: "100%"
                });

            },
            error: function(error) {
                console.error('Error fetching status:', error);
            }
        })
    }


    function add() {
        saveData = 'add';
        $('#modal-stock').modal('show');
        formData[0].reset();
        $(".modal-title").text("Setting Stock");
        $(".add-customer").text("Setting");
    }

    function byid(id) {

        var uuid = id;
        id_customer = id;
        saveData = 'edit';

        $('#modal-stock').modal('show');
        $(".modal-title").text("Update Stock");
        $(".add-product").text("Update");

        $.ajax({
            url: "{{ route('stocks.show', ':uuid') }}".replace(':uuid', uuid),
            method: 'get',
            dataType: "json",
            data: formData,
            success: function(response) {

                getProduct(response.data.product_id);
                getStatus(response.data.status);
                $("#product_qty").val(response.data.qty);
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
                    url = "{{ route('stocks.destroy', ':uuid') }}";
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
                        $('#modal-stock').hide();
                        $('#modal-stock').modal('hide');
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
            url = "{{ route('stocks.store') }}";
        } else if (saveData == 'edit') {
            url = "{{ route('stocks.update', ':uuid') }}";
            url = url.replace(':uuid', id_customer);
            method = 'PUT';
        } else if (saveData == 'delete') {

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
                $('#modal-stock').hide();
                $('#modal-stock').modal('hide');
                loadData();
                Swal.fire({
                    title: saveData + " Data Berhasil",
                    icon: "success"
                });
            },
            error: function(response) {

                console.log(response);

                if (response.responseJSON.errors.product_name != undefined) {
                    $('#error_product').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.product_qty != undefined) {
                    $('#error_qty').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.product_status != undefined) {
                    $('#error_status').css('visibility', 'visible');
                }

                Swal.fire({
                    title: saveData + " Data Gagal",
                    icon: "error"
                });

                $("#error_product").html(response.responseJSON.errors.product_name);
                $("#error_qty").html(response.responseJSON.errors.product_qty);
                $("#error_status").html(response.responseJSON.errors.product_status);


            }
        });

    });
</script>
@endsection