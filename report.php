<?php
include('functions.php');
?>
<?php
session_start();

if (!$_SESSION['id']) {
    header('location : login.php');
}
require('database.php');

?>
<!DOCTYPE html>
<html>

<head>

    <title>Report</title>

    <?php include "header.php"; ?>

</head>
<style>
    <?php
    include "main.css";
    ?>
</style>

<body>

<div id="wrapper">

    <?php
    include "navbar.php";
    include "topbar.php";
    ?>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-lg-12">
                <div class="ibox float-e-margins">
                    <div class="ibox-title">
                        <h5>Hours report list</h5>
                        <div class="ibox-tools">
                            <a class="collapse-link">
                                <i class="fa fa-chevron-up"></i>
                            </a>
                        </div>
                    </div>
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover dataTables" id="emptable">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th style="text-align:center">Name</th>
                                    <th style="text-align:center">Surname</th>
                                    <th style="text-align:center">Date</th>
                                    <th style="text-align:center">Nr of hours in</th>
                                    <th style="text-align:center">Nr of hours out</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>

<script>

    function render_row_details(row_details) {

        var table = "<table class='table table-hover test_id_per_qef' style='text-align: center'>" +
            "<thead>" +
            "<tr style='background-color: #737272;  color: white;'>" +
            "<th><center>Name</center></th>" +
            "<th><center>Surname</center></th>" +
            "<th><center>Date</center></th>" +
            "<th><center>hours_in</center></th>" +
            "<th><center>hours_out</center></th>" +
            "</tr>" +
            "</thead>" +
            "<tbody>";

        $.each(row_details, function (index, row_data) {
            table +=
                "<tr>" +
                "<td><center>" + row_data.name + "</center> </td>" +
                "<td><center>" + row_data.surname + "</center> </td>" +
                "<td><center>" + row_data.date + "</center></td>" +
                "<td><center>" + row_data.check_in + "</center></td>" +
                "<td><center>" + row_data.check_out + "</center></td>" +
                "<tr>"
        });

        return table;
    }

    $(document).ready(function () {

        var dt = $('#emptable').DataTable({

            processing: true,
            serverSide: true,
            paging: true,
            serverMethod: 'POST',
            ajax: {
                url: "report_fill.php",
                data: {}
            },
            columns: [

                {data: "actions"},
                {data: "name"},
                {data: "surname"},
                {data: "date"},
                {data: "hours_in"},
                {data: "hours_out"}
            ],
            columnsDefs: [
                {
                    orderable: false,
                    searchable: false,
                    targets: 0
                },
                {"targets": 4, "type": "date-eu"}
            ]
        });

        var detailRows = [];

        $('#emptable tbody').on('click', '.details-control', function () {

            var tr = $(this).parents('tr');
            var row = dt.row(tr);
            var idx = $.inArray(tr.attr('id'), detailRows);

                $(this).removeClass("fa-plus").addClass("fa-minus");

            if (row.child.isShown()) {
                $(this).removeClass("fa-minus").addClass("fa-plus");
                tr.removeClass('details bg-light');
                row.child.hide();
                // Remove from the 'open' array
                detailRows.splice(idx, 1);
            } else {
                $(this).removeClass("fa-plus").addClass("fa-minus");
                tr.addClass('details bg-light');


                var table_details = $(render_row_details(row.data().row_details));
                row.child(table_details).show();
                console.log($( row.child() ));
                console.log(table_details);
                console.log(table_details[0]);
                $(table_details[0]).DataTable();


                // $( row.child() ).DataTable();


                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
            }
        });
    });

</script>
