
<html>
    <head>
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.min.css" media="screen">
        <style>
            h1 {
                font-size: 20px;
                margin-top: 24px;
                margin-bottom: 24px;
            }
            img {
                height: 60px;
            }
        </style>
    </head>
    <body>
        <div class="col-md-8 offset-md-2 mt-5">
            <br>
            <h1>Package Install</h1>
            <div class="form-group">
                <label for="Vendor">Vendor</label>
                <input type="text" name="Vendor" class="form-control" id="Vendor" value="Athlo" required="required">
            </div>
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" name="name" class="form-control" id="name" value="PackageVoucher" required="required" >
            </div>
            <hr>
            <div class="form-group mt-3">
                <label class="mr-2">Upload your Package:</label>
                <input type="file" onchange="handleFile(this)" name="file" id="file">
            </div>
            <hr>
            <button type="submit" onclick="submit();" class="btn btn-primary">Submit</button>
        </div> 
    </body>
</html>
<script>
    var tempZip;
    function handleFile(e) {
        var file = e.files[0];
        var reader = new FileReader();
        reader.onload = function(event) {
          tempZip = event.target.result;
        };
        reader.readAsDataURL(file);
      }
    function submit()
    {
        let data = {
			"vender": document.getElementById("Vendor").value,
			"name":document.getElementById("name").value,
            "file":tempZip,
		};
        console.log(data);
		const url = "/api/installPackage";
		let xhr = new XMLHttpRequest();
		xhr.open('POST', url, true);
		xhr.setRequestHeader('Content-type', 'application/json');
		xhr.send(JSON.stringify(data));
		xhr.onload = function () {
            console.log(xhr.response);
		}
    }
</script>
