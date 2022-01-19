<html>
<head>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

</head>
<body>
<input type="text" name="daterange" value="01/01/2018 - 01/15/2018" />

</body>

<script>
    $(document).ready(function(){

        // Datapicker
        $( ".datepicker" ).datepicker({
            "dateFormat": "yy-mm-dd",
            changeYear: true
        });

        // DataTable
        var dataTable = $('#empTable').DataTable({
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'searching': true, // Set false to Remove default Search Control
            'ajax': {
                'url':'ajaxfile.php',
                'data': function(data){
                    // Read values
                    var from_date = $('#search_fromdate').val();
                    var to_date = $('#search_todate').val();

                    // Append to data
                    data.searchByFromdate = from_date;
                    data.searchByTodate = to_date;
                }
            },
            'columns': [
                { data: 'emp_name' },
                { data: 'email' },
                { data: 'date_of_joining' },
                { data: 'salary' },
                { data: 'city' },
            ]
        });

        // Search button
        $('#btn_search').click(function(){
            dataTable.draw();
        });

    });
</script>
