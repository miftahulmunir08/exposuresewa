@extends('layout.main')
@section('content')

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Category Table</h6>
        </div>
        <div class="card-body">
            <div class="float-right mb-4">
                <button href="#" class="btn btn-primary text-end" onclick="add()"><i class="fa fa-plus"></i>
                    Tambah Category</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" id="table-category" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Name</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <div class="modal" id="modal-category" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">Add Category</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form id="category_form">
                            <div class="modal-body">
                                <div class="form-group">
                                    <label for="category_name">Name</label>
                                    <input name="category_name" type="text" class="form-control" id="category_name" aria-describedby="emailHelp" placeholder="Enter Name">
                                    <small id="error_name" class="form-text text-danger">We'll never share your email with anyone else.</small>
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
    var formData = $('#category_form');
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
    });

    function loadData() {

        $('#table-category').DataTable({
            bDestroy: true,
            searching: true,
            processing: true,
            pagination: true,
            responsive: true,
            ordering: true,
            serverSide: true,
            ajax: "{{ route('data.category') }}",
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
        $('#modal-category').modal('show');
        formData[0].reset();
        $(".modal-title").text("Tambah Category");
        $(".add-category").text("Tambah");
    }

    function byid(id) {

        var uuid = id;
        id_category = id;
        saveData = 'edit';

        $('#modal-category').modal('show');
        $(".modal-title").text("Update Category");
        $(".add-customer").text("Update");

        $.ajax({
            url: "{{ route('categories.show', ':uuid') }}".replace(':uuid', uuid),
            method: 'get',
            dataType: "json",
            data: formData,
            success: function(response) {
                $("#category_name").val(response.data.name);
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
                    url = "{{ route('categories.destroy', ':uuid') }}";
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
            url = "{{ route('categories.store') }}";
        } else if (saveData == 'edit') {
            url = "{{ route('categories.update', ':uuid') }}";
            url = url.replace(':uuid', id_category);
            method = 'PUT';
        } else if (saveData == 'delete') {
            // url = "{{ route('categories.update', ':uuid') }}";
            // url = url.replace(':uuid', id_customer);
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
                $('#modal-category').hide();
                $('#modal-category').modal('hide');
                loadData();
                Swal.fire({
                    title: saveData + " Data Berhasil",
                    icon: "success"
                });
            },
            error: function(response) {

                console.log(response);

                if (response.responseJSON.errors.category_name != undefined) {
                    $('#error_name').css('visibility', 'visible');
                }

                Swal.fire({
                    title: saveData + " Data Gagal",
                    icon: "error"
                });

                $("#error_name").html(response.responseJSON.errors.category_name);

            }
        });

    });
</script>
@endsection