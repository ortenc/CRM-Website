<div class="container">
    <form method="post" action="" enctype="multipart/form-data" id="myform">
        <div class='preview'>
            <img src="" id="img" width="100" height="100">
        </div>
        <div >
            <input type="file" id="file" name="file" />
            <input type="button" class="button" value="Upload" id="but_upload">
        </div>
    </form>
</div>
<script>
    $(document).ready(function(){

        $("#but_upload").click(function(){

            var fd = new FormData();
            var files = $('#file')[0].files;

            // Check file selected or not
            if(files.length > 0 ){
                fd.append('file',files[0]);

                $.ajax({
                    url: 'upload.php',
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    success: function(response){
                        if(response != 0){
                            $("#img").attr("src",response);
                            $(".preview img").show(); // Display image element
                        }else{
                            alert('file not uploaded');
                        }
                    },
                });
            }else{
                alert("Please select a file.");
            }
        });
    });

</script>
