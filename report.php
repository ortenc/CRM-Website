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

    function format ( d ) {
        return 'Full name: '+d.name+' '+d.surname+'<br>'+
            'Salary: '+d.date+'<br>'+
            'The child row can contain any data you wish, including links, images, inner tables etc.';
    }

    $(document).ready(function () {

        var dataTable =  $('#emptable').DataTable({
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
                {data: "name"},
                {data: "surname"},
                {data: "date"},
                {data: "hours_in"},
                {data: "hours_out"}
            ],
        });


        $('#filter').click(function () {
            dataTable.draw();

        });

    });

</script>
