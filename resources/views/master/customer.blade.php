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
                    Tambah Customer</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-customer" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal" id="modal-customer" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
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
                                    <label for="customer_name">Name</label>
                                    <input name="customer_name" type="text" class="form-control" id="customer_name" aria-describedby="emailHelp" placeholder="Enter Name">
                                    <small id="error_name" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="customer_email">Email</label>
                                    <input name="customer_email" type="email" class="form-control" id="customer_email" aria-describedby="emailHelp" placeholder="Enter Email">
                                    <small id="error_email" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="customer_phone">Phone</label>
                                    <input name="customer_phone" type="text" class="form-control" id="customer_phone" aria-describedby="emailHelp" placeholder="Enter Phone">
                                    <small id="error_phone" class="form-text text-danger">We'll never share your email with anyone else.</small>
                                </div>

                                <div class="form-group">
                                    <label for="customer_jabatan">Jabatan</label>
                                    <input name="customer_jabatan" type="text" class="form-control" id="customer_jabatan" aria-describedby="emailHelp" placeholder="Enter Jabatan">
                                    <small id="error_jabatan" class="form-text text-danger">We'll never share your email with anyone else.</small>
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
        $('#error_name').css('visibility', 'hidden');
        $('#error_email').css('visibility', 'hidden');
        $('#error_phone').css('visibility', 'hidden');
        $('#error_jabatan').css('visibility', 'hidden');
    });

    function loadData() {

        $('#table-customer').DataTable({
            bDestroy: true,
            searching: true,
            processing: true,
            pagination: true,
            responsive: true,
            ordering: true,
            serverSide: true,
            ajax: "{{ route('data.customer') }}",
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
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'phone',
                    name: 'phone'
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


    function add() {
        saveData = 'add';
        $('#modal-customer').modal('show');
        formData[0].reset();
        $(".modal-title").text("Tambah Customer");
        $(".add-customer").text("Tambah");
    }

    function byid(id) {
        alert(id);

        var uuid = id;
        id_customer = id;
        saveData = 'edit';

        $('#modal-customer').modal('show');
        $(".modal-title").text("Update Customer");
        $(".add-customer").text("Update");

        $.ajax({
            url: "{{ route('customers.show', ':uuid') }}".replace(':uuid', uuid),
            method: 'get',
            dataType: "json",
            data: formData,
            success: function(response) {
                console.log(response);
                $("#customer_name").val(response.data.name);
                $("#customer_email").val(response.data.email);
                $("#customer_phone").val(response.data.phone);
                $("#customer_jabatan").val(response.data.jabatan);
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
                    url = "{{ route('customers.destroy', ':uuid') }}";
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
                        $('#modal-customer').hide();
                        $('#modal-customer').modal('hide');
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
            url = "{{ route('customers.store') }}";
        } else if (saveData == 'edit') {
            url = "{{ route('customers.update', ':uuid') }}";
            url = url.replace(':uuid', id_customer);
            method = 'PUT';
        } else if (saveData == 'delete') {
            url = "{{ route('customers.update', ':uuid') }}";
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
                $('#modal-customer').hide();
                $('#modal-customer').modal('hide');
                loadData();
                Swal.fire({
                    title: saveData + " Data Berhasil",
                    icon: "success"
                });
            },
            error: function(response) {

                console.log(response);

                if (response.responseJSON.errors.customer_name != undefined) {
                    $('#error_name').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.customer_email != undefined) {
                    $('#error_email').css('visibility', 'visible');
                }

                if (response.responseJSON.errors.customer_phone != undefined) {
                    $('#error_phone').css('visibility', 'visible');
                }

                Swal.fire({
                    title: saveData + " Data Gagal",
                    icon: "error"
                });

                $("#error_name").html(response.responseJSON.errors.customer_name);
                $("#error_email").html(response.responseJSON.errors.customer_email);
                $("#error_phone").html(response.responseJSON.errors.customer_phone);
                // $("#error_icon").html(response.responseJSON.errors.category_icon);

            }
        });

    });
</script>
@endsection