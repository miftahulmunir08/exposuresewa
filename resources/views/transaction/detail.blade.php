@extends('layout.main')
@section('content')

<style>
    .select2-container .select2-selection--single {
        height: 35px;
        display: flex;
        align-items: center;
        padding: 0 5px;
        border-radius: 8px;
    }
</style>

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">

            <div class="row">
                <div class="col-6">
                    <p>Create Order</p>
                </div>
                <div class="col-6">
                    <div class="float-right">
                        <a href="/transaction" class="btn btn-danger text-end"><i class="fa fa-left"></i>
                            Back</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body">
            <form class="form-inline">
                <div class="form-group mb-2">
                    <label for="staticEmail2" class="sr-only">Email</label>
                    <select name="product_name" id="product-select2" class="form-select form-control select2" style="width: 100%;">
                        <option value="" disabled selected>Select a product</option>
                    </select>
                    <input type="hidden" name="transaction_code" id="transaction_code" value="{{ $transaction_code }}" />
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="qty" class="sr-only">Qty</label>
                    <input type="number" min="0" class="form-control" id="qty" placeholder="qty">
                </div>
                <button type="submit" class="btn btn-primary mb-2">Add Item</button>
            </form>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">

            <div class="row">
                <div class="col-6">
                    <p>Product</p>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="table-transaction" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product Name</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal" id="modal-transaksi" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Tambah Transaksi</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="stock_form">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="customer">Customer</label>
                                    <select name="customer" id="customer-select2" class="form-select select2" style="width: 100%;">
                                        <option value="" disabled selected>Select a category</option>
                                    </select>
                                    <small id="error_customer" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_pinjam">Tanggal Peminjaman</label>
                                    <input name="tanggal_pinjam" type="date" class="form-control" id="tanggal_pinjam" aria-describedby="emailHelp" placeholder="Enter Tanggal Pinjam">
                                    <small id="error_tanggal_pinjam" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="tanggal_kembali">Tanggal Kembali</label>
                                    <input name="tanggal_kembali" type="date" class="form-control" id="tanggal_kembali" aria-describedby="emailHelp" placeholder="Enter Tanggal Pinjam">
                                    <small id="error_tanggal_kembali" class="form-text text-danger">We'll never share your email with anyone else.</small>
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
        $('#error_customer').css('visibility', 'hidden');
        $('#error_tanggal_pinjam').css('visibility', 'hidden');
        $('#error_tanggal_kembali').css('visibility', 'hidden');
    });

    function loadData() {
        $('#table-transaction-cart').DataTable({
            bDestroy: true,
            searching: true,
            processing: true,
            pagination: true,
            responsive: true,
            ordering: true,
            serverSide: true,
            ajax: {
                url: "{{ route('data.transaction-cart') }}",
                data: function(d) {
                    d.transaction_code = $('#transaction_code').val(); // Ambil nilai dari input field
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'no',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'transaction_code',
                    name: 'transaction_code'
                },
                {
                    data: 'customer_id',
                    name: 'customer_id'
                },
                {
                    data: 'tanggal_pinjam',
                    name: 'tanggal_pinjam'
                },
                {
                    data: 'tanggal_kembali',
                    name: 'tanggal_kembali'
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

    function getCustomer(customer_id) {
        let url = "{{ route('data.customer.all')}}"; // Ambil semua kategori

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                // console.log(response);
                // if (!response.data) return console.error('Invalid response format:', response);

                let html = '<option value="">Silahkan Pilih Product</option>';
                response.data.forEach(customer => {
                    let selected = (customer_id && customer.uuid == customer_id) ? ' selected' : '';
                    html += `<option value="${customer.uuid}"${selected}>${customer.name}</option>`;
                });

                $("#customer-select2").html(html).select2({
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



    function add() {
        saveData = 'add';
        $('#modal-transaksi').modal('show');
        formData[0].reset();
        $(".modal-title").text("Setting Stock");
        $(".add-customer").text("Setting");
    }

    function byid(id) {

        var uuid = id;
        id_transaction = id;
        saveData = 'edit';

        $('#modal-transaksi').modal('show');
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
            url = "{{ route('transactions.store') }}";
        } else if (saveData == 'edit') {
            url = "{{ route('transactions.update', ':uuid') }}";
            url = url.replace(':uuid', id_transaction);
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
                $('#modal-transaksi').hide();
                $('#modal-transaksi').modal('hide');
                loadData();
                Swal.fire({
                    title: saveData + " Data Berhasil",
                    icon: "success"
                });
            },
            error: function(response) {

                console.log(response);

                if (response.responseJSON.errors.customer != undefined) {
                    $('#error_customer').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.tanggal_pinjam != undefined) {
                    $('#error_tanggal_pinjam').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.tanggal_kembali != undefined) {
                    $('#error_tanggal_kembali').css('visibility', 'visible');
                }

                Swal.fire({
                    title: saveData + " Data Gagal",
                    icon: "error"
                });

                $("#error_product").html(response.responseJSON.errors.customer);
                $("#error_tanggal_pinjam").html(response.responseJSON.errors.tanggal_pinjam);
                $("#error_tanggal_kembali").html(response.responseJSON.errors.tanggal_kembali);


            }
        });

    });

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
</script>
@endsection