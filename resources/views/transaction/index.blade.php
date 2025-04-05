@extends('layout.main')
@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Transaction Table</h6>
        </div>
        <div class="card-body">
            <div class="float-right mb-4">
                <!-- <button href="#" class="btn btn-primary text-end" onclick="add()"><i class="fa fa-plus"></i>
                    Tambah Transaksi</button> -->
                <a href="{{route('transaction.create')}}" class="btn btn-primary text-end"><i class="fa fa-plus"></i>
                    Tambah Transaksi</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-transaction" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Transaksi</th>
                            <th>Customer</th>
                            <th>Tanggal Peminjaman</th>
                            <th>Tanggal Kembali</th>
                            <th>Tanggal Pengembalian</th>
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
                                <div class="mb-0">
                                    <label for="customer">Customer</label>
                                    <select name="customer" id="customer-select2" class="form-select select2" style="width: 100%;" disabled>
                                        <option value="" disabled selected>Select a category</option>
                                    </select>
                                    <input name="transaction_code" id="transaction_code" />
                                    <small id="error_customer" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="mb-0">
                                    <label for="tanggal_pinjam">Tanggal Peminjaman</label>
                                    <input name="tanggal_pinjam" type="text" class="form-control" id="tanggal_pinjam" aria-describedby="emailHelp" placeholder="Enter Tanggal Pinjam" disabled>
                                    <small id="error_tanggal_pinjam" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="mb-0">
                                    <label for="tanggal_kembali">Tanggal Kembali</label>
                                    <input name="tanggal_kembali" type="text" class="form-control" id="tanggal_kembali" aria-describedby="emailHelp" placeholder="Enter Tanggal Pinjam" disabled>
                                    <small id="error_tanggal_kembali" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="mb-0">
                                    <label for="tanggal_pengembalian">Tanggal Pengembalian</label>
                                    <input name="tanggal_pengembalian" type="date" class="form-control" id="tanggal_pengembalian" aria-describedby="emailHelp" placeholder="Enter Tanggal Pengembalian">
                                    <small id="error_tanggal_pengembalian" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="mb-0">
                                    <label for="telat">Telat</label>
                                    <input name="telat" type="text" class="form-control" id="telat" aria-describedby="emailHelp" placeholder="Telat" disabled>
                                </div>

                                <div class="mb-0">
                                    <label for="denda_bayar">Denda&nbsp;Bayar</label>
                                    <input name="denda_bayar" type="text" class="form-control" id="denda_bayar" aria-describedby="emailHelp" placeholder="Denda" disabled>
                                    <input name="denda_bayar_2" type="hidden" class="form-control" id="denda_bayar_2" aria-describedby="emailHelp" placeholder="Denda">
                                </div>

                                <!-- <p>Total Harga :</p>
                                <h3 id="total_harga">25.000</h3> -->

                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary add-product">Simpan</button>
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
    var transaction_code;
    var url, method;
    var selisihHari = 0;


    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Authorization': 'Bearer ' + $('meta[name="api-token"]').attr('content')
        }
    });

    $(document).ready(function() {
        loadData();
        getCustomer();
        $('#error_customer').css('visibility', 'hidden');
        $('#error_tanggal_pinjam').css('visibility', 'hidden');
        $('#error_tanggal_kembali').css('visibility', 'hidden');

    });

    function loadData() {

        $('#table-transaction').DataTable({
            bDestroy: true,
            searching: true,
            processing: true,
            pagination: true,
            responsive: true,
            ordering: true,
            serverSide: true,
            ajax: "{{ route('data.transaction') }}",
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
                    data: 'tanggal_pengembalian',
                    name: 'tanggal_pengembalian'
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

    function getCountCart(transaction_code, telat) {
        let transactionCode = transaction_code; // Gantilah dengan variabel yang sesuai
        let url = "{{ route('data.transaction-cart.count-detail', ':transaction_code') }}".replace(':transaction_code', transactionCode);

        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $("#total_harga").text("Rp. " + (response.data * telat).toLocaleString("id-ID"));
                $("#telat").val(selisihHari);
                $("#denda_bayar").val((response.data * 0.1) * selisihHari);
                $("#denda_bayar_2").val((response.data * 0.1) * selisihHari);
            },
            error: function(error) {
                console.error('Error fetching categories:', error);
            }
        });
    }

    function calculateDays() {

        let startDate = new Date($("#tanggal_pinjam").val());
        let endDate = new Date($("#tanggal_kembali").val());
        let pengembalianDate = new Date($("#tanggal_pengembalian").val());

        // alert(startDate);
        // return;

        if (!isNaN(startDate) && !isNaN(endDate) && endDate >= startDate) {
            let difference = (pengembalianDate - endDate) / (1000 * 60 * 60 * 24); // Konversi ms ke hari
            $("#total-days").val(difference + " hari");
            selisihHari = difference;

            // return difference;
            // alert(difference);

            // let transactionCode = transaction_code; // Gantilah dengan variabel yang sesuai
            // let url = "{{ route('data.transaction-cart.count-detail', ':transaction_code') }}".replace(':transaction_code', transactionCode);

            // $.ajax({
            //     url: url,
            //     method: 'GET',
            //     success: function(response) {
            //         $("#total_harga").text("Rp. " + (response.data * difference).toLocaleString("id-ID"));
            //     },
            //     error: function(error) {
            //         console.error('Error fetching categories:', error);
            //     }
            // });
        }
    }

    function add() {
        saveData = 'add';
        $('#modal-transaksi').modal('show');
        formData[0].reset();
        $(".modal-title").text("Tambah Transaksi");
        $(".add-customer").text("Setting");
    }

    function byid(id) {

        var uuid = id;
        id_transaction = id;
        saveData = 'edit';

        $('#modal-transaksi').modal('show');
        $(".modal-title").text("Update Stock");
        $(".add-product").text("Pengembalian");

        $.ajax({
            url: "{{ route('transactions.show', ':uuid') }}".replace(':uuid', uuid),
            method: 'get',
            dataType: "json",
            data: formData,
            success: function(response) {

                getCustomer(response.data.customer_id);
                $("#tanggal_pinjam").val(response.data.tanggal_pinjam);
                $("#tanggal_kembali").val(response.data.tanggal_kembali);
                $("#transaction_code").val(response.data.transaction_code);
                transaction_code = response.data.transaction_code;
                $("#tanggal_pengembalian").on("change", function(e) {
                    calculateDays();
                    getCountCart(response.data.transaction_code, selisihHari);
                    // alert(selisihHari);
                });
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
                    url = "{{ route('transactions.destroy', ':uuid') }}";
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
        Swal.fire({
            title: "Sebelum Pengembalian Harap Cek Detail Barang Apa Saja Yang Dipinjam",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, confirm!"
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData(this);
                if (saveData == 'add') {
                    method = 'POST';
                    url = "{{ route('transactions.store') }}";
                } else if (saveData == 'edit') {
                    url = "{{ route('transactions.update', ':uuid') }}";
                    url = url.replace(':uuid', id_transaction);
                    method = 'PUT';
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
                        // console.log(response);
                        // $('#modal-transaksi').hide();
                        // $('#modal-transaksi').modal('hide');
                        loadData();
                        Swal.fire({
                            title: "Update" + " Data Berhasil",
                            icon: "success"
                        });
                    },
                    error: function(response) {

                        console.log(response);

                        Swal.fire({
                            title: saveData + " Data Gagal",
                            icon: "error"
                        });

                        // $("#error_product").html(response.responseJSON.errors.customer);
                        // $("#error_tanggal_pinjam").html(response.responseJSON.errors.tanggal_pinjam);
                        // $("#error_tanggal_kembali").html(response.responseJSON.errors.tanggal_kembali);
                    }
                });
            }
        });
        // return;

    });
</script>
@endsection