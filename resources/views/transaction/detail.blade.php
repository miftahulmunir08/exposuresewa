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
        <div class="card-body d-flex justify-content-center align-items-center">
            <form class="form-inline" id="add_cart_form">
                <div class="form-group mb-2">
                    <label for="staticEmail2" class="sr-only">Product</label>
                    <select name="product_name" id="product-select2" class="product-select2 form-select form-control select2" style="width: 100%;">
                        <option value="" disabled selected>Select a product</option>
                    </select>
                    <input type="hidden" name="transaction_code" id="transaction_code" value="{{ $transaction_code }}" />
                </div>
                <div class="form-group mx-sm-3 mb-2">
                    <label for="qty" class="sr-only">Qty</label>
                    <input name="qty" type="number" min="0" class="form-control" id="qty" placeholder="qty">
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
                <table class="table table-bordered" id="table-transaction-cart" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Product Name</th>
                            <th>Per Hari</th>
                            <th>Qty</th>
                            <th>Sub Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-6">
                        <div class="card-body">
                            <form class="form-inline" id="add_transaction">
                                <div class="form-group m-2 w-100">
                                    <select name="customer" id="customer-select2" class="customer-select2 form-select form-control select2" style="width: 100%;">
                                        <option value="" disabled selected>Select a customer</option>
                                    </select>
                                    <input type="hidden" name="transaction_code" id="transaction_code" value="{{ $transaction_code }}" />
                                </div>
                                <div class="form-group m-2">
                                    <input name="start_date" id="start-date" type="date" class="datepicker form-control" placeholder="start date" min="{{ date('Y-m-d') }}" />
                                </div>
                                <div class="form-group m-2">
                                    <input name="end_date" id="end-date" type="date" class="datepicker form-control" placeholder="end date" min="{{ date('Y-m-d') }}" />
                                </div>
                                <div class="form-group m-2">
                                    <label for="total-days" class="mr-3">Total Days</label>
                                    <input id="total-days" type="text" class="form-control" readonly />
                                </div>
                                <button type="submit" class="btn btn-info m-2">Update Transaction</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <p>Total Harga :</p>
                        <h3 id="total_harga">25.000</h3>
                    </div>
                </div>
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
                        <form id="transaction_cart_form">
                            <div class="modal-body">

                                <div class="form-group">
                                    <label for="product_name">Product</label>
                                    <select name="product_name" id="product-select2" class="form-select select2 product-select2" style="width: 100%;">
                                        <option value="" disabled selected>Select a category</option>
                                    </select>
                                    <small id="error_product" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="qty">Qty</label>
                                    <input type="hidden" name="transaction_code" id="transaction_code" value="{{ $transaction_code }}" />
                                    <input name="qty" type="number" class="form-control" id="qty_product" aria-describedby="emailHelp" placeholder="Enter Qty">
                                    <small id="error_qty" class="form-text text-danger">We'll never share your email with anyone else.</small>
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
    var formData = $('#add_cart_form');
    var formData2 = $('#transaction_cart_form');
    var formData3 = $('#add_transaction');
    var transaction_code = '{{ $transaction_code }}';

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
        getCustomer();
        getCountCart();
        get_transaction(transaction_code);
        $('#error_product').css('visibility', 'hidden');
        $('#error_qty').css('visibility', 'hidden');


        function calculateDays() {
            let startDate = new Date($("#start-date").val());
            let endDate = new Date($("#end-date").val());

            if (!isNaN(startDate) && !isNaN(endDate) && endDate >= startDate) {
                let difference = (endDate - startDate) / (1000 * 60 * 60 * 24); // Konversi ms ke hari
                $("#total-days").val(difference + " hari");

                let transactionCode = '{{ $transaction_code }}'; // Gantilah dengan variabel yang sesuai
                let url = "{{ route('data.transaction-cart.count-detail', ':transaction_code') }}".replace(':transaction_code', transactionCode);

                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function(response) {
                        $("#total_harga").text("Rp. " + (response.data * difference).toLocaleString("id-ID"));
                    },
                    error: function(error) {
                        console.error('Error fetching categories:', error);
                    }
                });
            } else {
                $("#total-days").val("");
            }
        }

        $("#start-date, #end-date").on("change", calculateDays);
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
                url: "{{ route('data.transaction-cart-detail') }}",
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
                    data: 'product_id',
                    name: 'product_id'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'qty',
                    name: 'qty'
                },
                {
                    data: 'total',
                    name: 'total'
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
                    placeholder: "Pilih Customer",
                    allowClear: true,
                    width: "100%"
                });
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });
    }

    function getCountCart() {
        let transactionCode = '{{ $transaction_code }}'; // Gantilah dengan variabel yang sesuai
        let url = "{{ route('data.transaction-cart.count-detail', ':transaction_code') }}".replace(':transaction_code', transactionCode);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $("#total_harga").text("Rp. " + response.data.toLocaleString("id-ID"));
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });
    }

    $(formData).submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        saveData = "tambah";
        url = "{{ route('transactions-cart.store') }}";

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
                // $('#modal-transaksi').hide();
                // $('#modal-transaksi').modal('hide');
                loadData();
                getCountCart();
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

    function add() {

        $("#product-select2").val();
        $("#transaction_code").val();
        $("#qty").val();

        const formData = new FormData(this);

        url = "{{ route('transactions-cart.store') }}";

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
                // $('#modal-transaksi').hide();
                // $('#modal-transaksi').modal('hide');
                loadData();
                getCountCart();
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

    }

    function byid(id) {

        var uuid = id;
        id_transaction = id;
        saveData = 'edit';

        $('#modal-transaksi').modal('show');
        $(".modal-title").text("Update Stock");
        $(".add-product").text("Update");

        $.ajax({
            url: "{{ route('transactions-cart.show', ':uuid') }}".replace(':uuid', uuid),
            method: 'get',
            dataType: "json",
            data: formData,
            success: function(response) {

                getProduct(response.data.product_id);
                $("#qty_product").val(response.data.qty);
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
                    url = "{{ route('transactions-cart.destroy', ':uuid') }}";
                    url = url.replace(':uuid', id);
                    method = 'DELETE';
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
                        getCountCart();
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

    $(formData2).submit(function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        saveData = "update";

        url = "{{ route('transactions-cart.update', ':uuid') }}";
        url = url.replace(':uuid', id_transaction);
        formData.append('_method', 'PUT');

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
                // $('#modal-transaksi').hide();
                // $('#modal-transaksi').modal('hide');
                loadData();
                getCountCart();
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

                $(".product-select2").html(html).select2({
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

    $(formData3).submit(function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, Sure!"
        }).then((result) => {
            if (result.isConfirmed) {

                let formData = new FormData(this);
                formData.append('_method', 'POST'); // Tambahkan metode jika perlu
                let url = "{{ route('transactions.store') }}"; // Rute API

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        // alert("sudah");
                        // return;
                        // console.log(response);
                        $('#modal-stock').hide();
                        $('#modal-stock').modal('hide');
                        Swal.fire({
                            title: " Transaksi Berhasil Dibuat",
                            icon: "success"
                        });
                        window.location.href = "transaction/"; // Ganti dengan URL tujuan
                    },
                    error: function(response) {

                        // console.log(response);
                        // alert("ss");
                        // return;

                        Swal.fire({
                            title: saveData + " Data Gagal",
                            icon: "error"
                        });

                    }
                });
            }
        });
    });


    function get_transaction(transaction_code) {
        
    }
</script>
@endsection