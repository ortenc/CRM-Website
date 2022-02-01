<?php
include('functions.php');
?>
<?php
session_start();

if(!$_SESSION['id'])
{
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
                            <table class="table table-striped table-bordered table-hover dataTables" id="emptable" >
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>Name</th>
                                    <th>Surname</th>
                                    <th>Date</th>
                                    <th>Nr of hours in</th>
                                    <th>Nr of hours out</th>
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
            "<tbody><tr style='background-color: #e2e2e2'>";



        $.each(row_details, function (index, row_data) {
            table +=
                "<tr>"+
                "<td><center>" + row_data.name + "</center> </td>" +
                "<td><center>" + row_data.surname + "</center> </td>" +
                "<td><center>" + row_data.date + "</center></td>" +
                "<td><center>" + row_data.check_in + "</center></td>" +
                "<td><center>" + row_data.check_out + "</center></td>" +
            "<tr>"

        });

        table += "</tr></tbody></table>";



        return table;



    }

   var test_id_per_qef = $('.test_id_per_qef').DataTable({
        dom: '<"html5buttons"B>lTfgitp',
        buttons: [
            {extend: 'excel', title: 'Data'},
        ],
        columnsDefs: [
            {
                orderable: true,
                searchable: true,
                targets: 0
            },
        ]
    });



    $(document).ready(function () {

        var dt =  $('#emptable').DataTable({
            processing: true,
            serverSide: true,
            paging: true,
            serverMethod: 'POST',
            ajax: {
                url: "report_fill.php",
                data:  {
                }
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
                {"targets":4, "type":"date-eu"}
            ]
        });

        // Array to track the ids of the details displayed rows

        var detailRows = [];

        $('#emptable tbody').on('click', '.details-control', function () {

            var tr = $(this).parents('tr');
            var row = test_id_per_qef.row(tr);
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
                row.child(render_row_details(row.data().row_details)).show();
                // Add to the 'open' array
                if (idx === -1) {
                    detailRows.push(tr.attr('id'));
                }
            }

        });
        // On each draw, loop over the `detailRows` array and show any child rows
        dt.on('draw', function () {
            $.each(detailRows, function (i, id) {
                $('#' + id + ' td:first-child').trigger('click');
            });
        });



        $('#filter').click(function () {
            dataTable.draw();

        });

    });

</script>
